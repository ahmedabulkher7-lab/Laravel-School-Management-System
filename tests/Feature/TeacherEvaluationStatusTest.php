<?php

namespace Tests\Feature;

use App\Models\DailyProgress;
use App\Models\GradeLevel;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeacherEvaluationStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_logged_and_remaining_students_for_each_teacher(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $gradeLevel = GradeLevel::create(['name' => 'الصف الأول', 'order' => 1, 'track' => 'arabic']);
        $subject = Subject::create(['name' => 'Science', 'name_ar' => 'العلوم']);
        $gradeLevel->subjects()->attach($subject);

        $teacherUser = User::factory()->create(['name' => 'أحمد مدرس']);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'full_name' => 'أحمد مدرس']);
        $teacher->subjects()->attach($subject);
        $teacher->gradeLevels()->attach($gradeLevel);

        $loggedStudent = $this->createStudent('سارة المسجلة', $gradeLevel);
        $remainingStudent = $this->createStudent('عمر المتبقي', $gradeLevel);

        DailyProgress::create([
            'student_id' => $loggedStudent->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-08-09',
            'attendance_status' => 'present',
            'interaction_level' => 'engaged',
            'homework_submitted' => true,
            'score' => 9,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.teacher-evaluation-status.index', [
            'date' => '2026-08-09',
        ]));

        $response->assertOk();
        $response->assertSee('أحمد مدرس');
        $response->assertSee('سارة المسجلة');
        $response->assertSee('عمر المتبقي');
        $response->assertSee('تم التسجيل لهم');
        $response->assertSee('الطلاب المتبقون');
    }

    private function createStudent(string $name, GradeLevel $gradeLevel): Student
    {
        $user = User::factory()->create(['name' => $name]);

        return Student::create([
            'user_id' => $user->id,
            'grade_level_id' => $gradeLevel->id,
            'full_name' => $name,
            'date_of_birth' => '2015-01-01',
            'guardian_name' => 'ولي أمر',
            'guardian_phone' => '01000000000',
            'enrollment_date' => '2025-09-01',
            'track' => 'arabic',
        ]);
    }
}
