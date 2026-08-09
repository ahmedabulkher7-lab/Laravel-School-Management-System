<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use App\Models\Teacher;
use App\Models\WeeklyPlan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WeeklyPlanSeeder extends Seeder
{
    private const EXCLUDED_GRADE_LEVEL_ID = 3;

    public function run(): void
    {
        $weekStart = Carbon::now()->next(Carbon::SUNDAY)->startOfDay();
        $teachers = Teacher::with(['subjects', 'gradeLevels'])->get();
        $gradeLevels = GradeLevel::with(['subjects', 'teachers.subjects'])->get();

        foreach ($gradeLevels as $gradeLevel) {
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
                    $teacher->subjects->push($subject);
                }
            }
        }

        $gradeLevels = GradeLevel::with(['subjects', 'teachers.subjects'])->get();

        foreach ($gradeLevels as $gradeLevel) {
            if ($gradeLevel->id === self::EXCLUDED_GRADE_LEVEL_ID) {
                continue;
            }

            foreach ($gradeLevel->subjects as $subject) {
                $responsibleTeachers = $gradeLevel->teachers->filter(
                    fn ($teacher) => $teacher->subjects->contains('id', $subject->id)
                );

                foreach ($responsibleTeachers as $teacher) {
                    WeeklyPlan::updateOrCreate(
                        [
                            'teacher_id' => $teacher->id,
                            'grade_level_id' => $gradeLevel->id,
                            'subject_id' => $subject->id,
                            'week_start' => $weekStart->toDateString(),
                        ],
                        [
                            'class_work' => "Unit 4 - {$subject->name} lesson 1",
                            'homework' => "حل تدريبات {$subject->name} ومراجعة الدرس",
                            'online_games' => "نشاط تفاعلي {$subject->name}",
                        ]
                    );
                }
            }
        }

        $this->command?->info(
            "Weekly plans seeded for week {$weekStart->toDateString()}, excluding grade level "
            . self::EXCLUDED_GRADE_LEVEL_ID . '.'
        );
    }
}
