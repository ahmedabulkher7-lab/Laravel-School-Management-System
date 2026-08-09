<?php

namespace Database\Seeders;

use App\Models\DailyProgress;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\WeeklyReportService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WeeklyReportTestSeeder extends Seeder
{
    public function run(): void
    {
        $weekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY)->startOfDay();
        $teachers = Teacher::with(['subjects', 'gradeLevels'])->get();
        $students = Student::with(['gradeLevel.subjects', 'gradeLevel.teachers.subjects'])->get();
        $createdRecords = 0;
        $generatedReports = 0;

        foreach ($students as $student) {
            $gradeLevel = $student->gradeLevel;
            if (!$gradeLevel) {
                continue;
            }

            foreach ($gradeLevel->subjects as $subject) {
                $teacher = $gradeLevel->teachers->first(
                    fn ($assignedTeacher) => $assignedTeacher->subjects->contains('id', $subject->id)
                );

                if (!$teacher) {
                    $teacher = $teachers->first(
                        fn ($candidate) => $candidate->subjects->contains('id', $subject->id)
                    ) ?? $teachers->first();

                    if (!$teacher) {
                        continue;
                    }

                    $teacher->subjects()->syncWithoutDetaching([$subject->id]);
                    $teacher->gradeLevels()->syncWithoutDetaching([$gradeLevel->id]);
                    $gradeLevel->teachers->push($teacher);
                }

                for ($day = 0; $day < 5; $day++) {
                    $date = $weekStart->copy()->addDays($day)->toDateString();
                    $progress = DailyProgress::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'date' => $date,
                        ],
                        [
                            'teacher_id' => $teacher->id,
                            'attendance_status' => 'present',
                            'interaction_level' => 'engaged',
                            'homework_submitted' => true,
                            'score' => 8.5,
                            'comment' => 'أداء جيد ومستوى متقدم',
                        ]
                    );
                    $createdRecords += $progress->wasRecentlyCreated ? 1 : 0;
                }
            }
        }

        $service = app(WeeklyReportService::class);
        foreach ($students as $student) {
            if ($service->generateIfReady($student->fresh(), $weekStart)) {
                $generatedReports++;
            }
        }

        $this->command?->info("Seeded {$createdRecords} weekly progress records for {$weekStart->toDateString()}.");
        $this->command?->info("Generated or confirmed {$generatedReports} weekly reports.");
    }
}
