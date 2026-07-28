<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\GradeLevel;
use App\Models\TeacherStudentAssignment;
use App\Models\DailyProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class FakeDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Subjects
        $subjects = [
            ['name' => 'Math', 'name_ar' => 'الرياضيات'],
            ['name' => 'Science', 'name_ar' => 'العلوم'],
            ['name' => 'Arabic', 'name_ar' => 'اللغة العربية'],
            ['name' => 'English', 'name_ar' => 'اللغة الإنجليزية'],
            ['name' => 'Islamic Studies', 'name_ar' => 'التربية الإسلامية'],
            ['name' => 'Social Studies', 'name_ar' => 'الدراسات الاجتماعية'],
        ];

        $subjectModels = [];
        foreach ($subjects as $sub) {
            $subjectModels[] = Subject::firstOrCreate(['name' => $sub['name']], $sub);
        }

        // 2. Create Teachers
        $teacherRole = Role::findByName('teacher');
        $teachers = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['email' => "teacher{$i}@school.com"],
                [
                    'name' => "معلم تجريبي $i",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole($teacherRole);

            $teachers[] = Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'subject_id' => collect($subjectModels)->random()->id,
                    'full_name'  => "معلم تجريبي $i",
                    'phone'      => "0123456789$i",
                ]
            );
        }

        // 3. Create Students
        $studentRole = Role::findByName('student');
        $gradeLevels = GradeLevel::all();
        if ($gradeLevels->isEmpty()) {
            return; // Needs GradeLevels seeded first
        }

        $students = [];
        for ($i = 1; $i <= 20; $i++) {
            $user = User::firstOrCreate(
                ['email' => "student{$i}@school.com"],
                [
                    'name' => "طالب تجريبي $i",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole($studentRole);

            $students[] = Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'grade_level_id' => $gradeLevels->random()->id,
                    'full_name' => "طالب تجريبي $i",
                    'date_of_birth' => now()->subYears(rand(6, 18))->toDateString(),
                    'guardian_name' => "ولي أمر الطالب $i",
                    'guardian_phone' => "0109876543$i",
                    'enrollment_date' => now()->subDays(rand(10, 100)),
                ]
            );
        }

        // 4. Create Assignments
        foreach ($students as $student) {
            // Assign 3 random subjects and teachers to each student
            $assignedSubjects = collect($subjectModels)->random(3);
            foreach ($assignedSubjects as $subject) {
                $teacher = collect($teachers)->random();
                TeacherStudentAssignment::firstOrCreate([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                ]);
            }
        }

        // 5. Create Daily Progress (for the last 5 days)
        $statuses = ['present', 'present', 'present', 'absent', 'late'];
        $interactions = ['engaged', 'engaged', 'not_engaged'];
        
        $assignments = TeacherStudentAssignment::all();
        foreach ($assignments as $assignment) {
            for ($d = 0; $d < 5; $d++) {
                $date = Carbon::today()->subDays($d);
                // Skip weekends
                if ($date->isWeekend()) continue;

                DailyProgress::firstOrCreate(
                    [
                        'student_id' => $assignment->student_id,
                        'subject_id' => $assignment->subject_id,
                        'date'       => $date->toDateString(),
                    ],
                    [
                        'teacher_id' => $assignment->teacher_id,
                        'attendance_status' => $statuses[array_rand($statuses)],
                        'interaction_level' => $interactions[array_rand($interactions)],
                        'homework_submitted' => (bool)rand(0, 1),
                        'score' => rand(5, 10),
                        'comment' => 'أداء جيد ومستوى متقدم',
                    ]
                );
            }
        }
    }
}
