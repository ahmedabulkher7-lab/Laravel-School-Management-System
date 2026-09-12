<?php

namespace App\Services;

use App\Models\DailyProgress;
use App\Models\GradeLevel;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TeacherProgressLogService
{
    /**
     * Build the daily progress log dataset restricted to today's active scheduled timetable.
     *
     * @return array{
     *     hasSchedulesToday: bool,
     *     subjects: Collection<int, Subject>,
     *     selectedSubject: ?Subject,
     *     tracks: Collection<int, string>,
     *     gradeLevels: Collection<int, GradeLevel>,
     *     subjectGradeLevels: Collection<int, GradeLevel>,
     *     subjectCompletion: Collection<int, array{total: int, completed: int, complete: bool}>,
     *     trackCompletion: Collection<string, array{total: int, completed: int, complete: bool}>,
     *     gradeCompletion: Collection<string, array{total: int, completed: int, complete: bool}>,
     *     filteredGradeLevels: Collection<int, GradeLevel>,
     *     students: Collection<int, Student>,
     *     selectedTrack: ?string,
     *     selectedGradeLevel: ?GradeLevel,
     *     selectedSchedule: ?Schedule,
     *     today: string
     * }
     */
    public function getProgressLogData(
        ?Teacher $teacher,
        Carbon $date,
        ?int $selectedSubjectId = null,
        ?string $selectedTrack = null,
        ?int $selectedGradeLevelId = null
    ): array {
        $today = $date->toDateString();

        if (!$teacher) {
            return $this->emptyState($today);
        }

        // 1. Single query: Fetch today's scheduled lessons for this teacher without cancelled exceptions
        $schedules = Schedule::query()
            ->with([
                'gradeLevel:id,name,track,order',
                'subject:id,name,name_ar,color',
            ])
            ->where('teacher_id', $teacher->id)
            ->where('day_of_week', $date->dayOfWeek)
            ->whereDoesntHave('exceptions', fn ($query) => $query->whereDate('date', $today))
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return $this->emptyState($today);
        }

        $scheduledGradeLevelIds = $schedules->pluck('grade_level_id')->unique()->values();
        $scheduledSubjectIds = $schedules->pluck('subject_id')->unique()->values();

        // Distinct subjects and grade levels that have lessons scheduled TODAY
        $subjects = $schedules->map->subject->unique('id')->sortBy('name')->values();
        $gradeLevels = $schedules->map->gradeLevel->unique('id')->sortBy(['track', 'order'])->values();

        // 2. Single batch query: Fetch students enrolled in today's scheduled grades
        $studentsByGrade = Student::query()
            ->whereIn('grade_level_id', $scheduledGradeLevelIds)
            ->with('gradeLevel:id,name,track')
            ->orderBy('full_name')
            ->get()
            ->groupBy('grade_level_id');

        // 3. Single batch query: Fetch all progress logged today for this teacher
        $loggedStudentIdsBySubject = [];
        DailyProgress::query()
            ->where('teacher_id', $teacher->id)
            ->whereDate('date', $today)
            ->where(function ($query) use ($schedules, $scheduledSubjectIds) {
                $query->whereIn('schedule_id', $schedules->pluck('id'))
                    ->orWhere(function ($subQuery) use ($scheduledSubjectIds) {
                        $subQuery->whereNull('schedule_id')
                            ->whereIn('subject_id', $scheduledSubjectIds);
                    });
            })
            ->get(['student_id', 'subject_id'])
            ->each(function (DailyProgress $progress) use (&$loggedStudentIdsBySubject): void {
                $loggedStudentIdsBySubject[$progress->subject_id][$progress->student_id] = true;
            });

        // 4. In-Memory: Build completion stats for each scheduled subject and grade
        $gradeCompletion = collect();
        $subjectCompletion = collect();

        foreach ($subjects as $subject) {
            $subjectSchedules = $schedules->where('subject_id', $subject->id);
            $subjectGrades = $subjectSchedules->map->gradeLevel->unique('id')->values();

            $subjectGradeStats = $subjectGrades->map(function (GradeLevel $gradeLevel) use (
                $subject,
                $studentsByGrade,
                $loggedStudentIdsBySubject,
                $gradeCompletion
            ): array {
                $gradeStudents = $studentsByGrade->get($gradeLevel->id, collect());
                $completed = $gradeStudents->filter(
                    fn (Student $student) => isset($loggedStudentIdsBySubject[$subject->id][$student->id])
                )->count();

                $total = $gradeStudents->count();
                $status = [
                    'total' => $total,
                    'completed' => $completed,
                    'complete' => $total > 0 && $completed === $total,
                ];

                $gradeCompletion->put($subject->id . '-' . $gradeLevel->id, $status);

                return $status;
            });

            $subjectCompletion->put($subject->id, [
                'total' => $subjectGradeStats->sum('total'),
                'completed' => $subjectGradeStats->sum('completed'),
                'complete' => $subjectGradeStats->isNotEmpty()
                    && $subjectGradeStats->every(fn (array $s) => $s['complete']),
            ]);
        }

        // Step 1: Filter selected subject
        $selectedSubject = $subjects->firstWhere('id', (int) $selectedSubjectId);
        $subjectGradeLevels = $selectedSubject
            ? $schedules->where('subject_id', $selectedSubject->id)->map->gradeLevel->unique('id')->values()
            : collect();

        // Step 2: Available tracks for selected subject today
        $tracks = $subjectGradeLevels
            ->map(fn (GradeLevel $gradeLevel) => $gradeLevel->track->value)
            ->unique()
            ->values();

        $trackCompletion = $tracks->mapWithKeys(function (string $track) use (
            $subjectGradeLevels,
            $selectedSubject,
            $gradeCompletion
        ): array {
            $trackGrades = $subjectGradeLevels->filter(fn (GradeLevel $gl) => $gl->track->value === $track);
            $trackStats = $trackGrades->map(fn (GradeLevel $gl) => $gradeCompletion->get($selectedSubject->id . '-' . $gl->id));

            return [$track => [
                'total' => $trackStats->sum('total'),
                'completed' => $trackStats->sum('completed'),
                'complete' => $trackStats->isNotEmpty()
                    && $trackStats->every(fn (array $status) => $status['complete']),
            ]];
        });

        if (!$tracks->contains($selectedTrack)) {
            $selectedTrack = null;
        }

        // Step 3: Available grades for selected track today
        $filteredGradeLevels = $selectedTrack
            ? $subjectGradeLevels->filter(fn (GradeLevel $gl) => $gl->track->value === $selectedTrack)->values()
            : collect();

        $selectedGradeLevel = $filteredGradeLevels->firstWhere('id', (int) $selectedGradeLevelId);

        // Step 4: Students in the selected grade level
        $students = collect();
        $selectedSchedule = null;
        if ($selectedGradeLevel && $selectedSubject) {
            $students = $studentsByGrade->get($selectedGradeLevel->id, collect());
            $selectedSchedule = $schedules->first(
                fn (Schedule $s) => $s->grade_level_id === $selectedGradeLevel->id && $s->subject_id === $selectedSubject->id
            );
        }

        return [
            'hasSchedulesToday' => true,
            'subjects' => $subjects,
            'selectedSubject' => $selectedSubject,
            'tracks' => $tracks,
            'gradeLevels' => $gradeLevels,
            'subjectGradeLevels' => $subjectGradeLevels,
            'subjectCompletion' => $subjectCompletion,
            'trackCompletion' => $trackCompletion,
            'gradeCompletion' => $gradeCompletion,
            'filteredGradeLevels' => $filteredGradeLevels,
            'students' => $students,
            'selectedTrack' => $selectedTrack,
            'selectedGradeLevel' => $selectedGradeLevel,
            'selectedSchedule' => $selectedSchedule,
            'today' => $today,
        ];
    }

    /**
     * Safe empty state when teacher has no scheduled lessons today.
     *
     * @return array<string, mixed>
     */
    private function emptyState(string $today): array
    {
        return [
            'hasSchedulesToday' => false,
            'subjects' => collect(),
            'selectedSubject' => null,
            'tracks' => collect(),
            'gradeLevels' => collect(),
            'subjectGradeLevels' => collect(),
            'subjectCompletion' => collect(),
            'trackCompletion' => collect(),
            'gradeCompletion' => collect(),
            'filteredGradeLevels' => collect(),
            'students' => collect(),
            'selectedTrack' => null,
            'selectedGradeLevel' => null,
            'selectedSchedule' => null,
            'today' => $today,
        ];
    }
}
