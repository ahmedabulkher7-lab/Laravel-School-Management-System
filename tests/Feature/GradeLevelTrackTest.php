<?php

namespace Tests\Feature;

use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GradeLevelTrackTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_same_grade_name_can_be_created_once_per_section(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->post(route('admin.grade-levels.store'), [
            'name' => 'الصف الأول الإعدادي',
            'order' => 7,
            'track' => 'arabic',
        ])->assertSessionDoesntHaveErrors();

        $this->actingAs($admin)->post(route('admin.grade-levels.store'), [
            'name' => 'الصف الأول الإعدادي',
            'order' => 7,
            'track' => 'languages',
        ])->assertSessionDoesntHaveErrors();

        $this->assertDatabaseCount('grade_levels', 2);
        $this->assertDatabaseHas('grade_levels', [
            'name' => 'الصف الأول الإعدادي',
            'track' => 'arabic',
        ]);
        $this->assertDatabaseHas('grade_levels', [
            'name' => 'الصف الأول الإعدادي',
            'track' => 'languages',
        ]);
    }

    public function test_a_shared_grade_can_be_assigned_to_an_arabic_or_languages_teacher(): void
    {
        $admin = $this->adminUser();
        Role::findOrCreate('teacher');
        $sharedGradeLevel = GradeLevel::create([
            'name' => 'الصف المشترك',
            'order' => 1,
            'track' => 'both',
        ]);
        $subject = Subject::create(['name' => 'Mathematics']);
        $subject->gradeLevels()->attach($sharedGradeLevel);

        $this->assertSame('both', $sharedGradeLevel->track->value);

        $response = $this->actingAs($admin)->post(route('admin.teachers.store'), [
            'full_name' => 'معلم عربي',
            'email' => 'arabic-teacher@example.test',
            'password' => 'password123',
            'track' => 'arabic',
            'subject_ids' => [$subject->id],
            'grade_level_ids' => [$sharedGradeLevel->id],
        ]);

        $response->assertRedirect(route('admin.teachers.index'))
            ->assertSessionDoesntHaveErrors();
        $teacher = Teacher::where('full_name', 'معلم عربي')->firstOrFail();
        $this->assertTrue($teacher->gradeLevels->contains($sharedGradeLevel));
    }

    public function test_a_student_cannot_be_assigned_to_a_grade_level_from_another_section(): void
    {
        $admin = $this->adminUser();
        $languagesGradeLevel = GradeLevel::create([
            'name' => 'الصف الأول الإعدادي',
            'order' => 7,
            'track' => 'languages',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.students.create'))
            ->post(route('admin.students.store'), [
                'full_name' => 'طالب تجريبي',
                'email' => 'student@example.test',
                'password' => 'password123',
                'grade_level_id' => $languagesGradeLevel->id,
                'track' => 'arabic',
                'enrollment_date' => '2026-09-01',
            ]);

        $response->assertRedirect(route('admin.students.create'));
        $response->assertSessionHasErrors('grade_level_id');
        $this->assertDatabaseMissing('students', ['full_name' => 'طالب تجريبي']);
    }

    private function adminUser(): User
    {
        Role::findOrCreate('admin');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }
}
