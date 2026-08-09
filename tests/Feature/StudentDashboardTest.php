<?php

namespace Tests\Feature;

use App\Models\GradeLevel;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_student_can_view_their_dashboard_and_subject_count(): void
    {
        $role = Role::create(['name' => 'student']);
        $user = User::factory()->create();
        $user->assignRole($role);

        $gradeLevel = GradeLevel::create(['name' => 'Grade 4', 'order' => 4, 'track' => 'arabic']);
        $gradeLevel->subjects()->attach([
            Subject::create(['name' => 'Mathematics'])->id,
            Subject::create(['name' => 'Science'])->id,
        ]);

        Student::create([
            'user_id' => $user->id,
            'grade_level_id' => $gradeLevel->id,
            'full_name' => $user->name,
            'date_of_birth' => '2015-01-01',
            'guardian_name' => 'Guardian Name',
            'guardian_phone' => '01000000000',
            'enrollment_date' => '2025-09-01',
            'track' => 'arabic',
        ]);

        $response = $this->actingAs($user)->get(route('student.dashboard'));

        $response->assertOk();
        $response->assertSee('>2<', false);
    }
}
