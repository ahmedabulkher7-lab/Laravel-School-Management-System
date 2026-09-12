<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeacherPasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_change_their_password_with_the_current_password(): void
    {
        $teacher = $this->teacherUser('old-password');

        $this->actingAs($teacher)
            ->get(route('teacher.account.password.edit'))
            ->assertOk()
            ->assertSee('تغيير كلمة المرور');

        $response = $this->actingAs($teacher)->put(route('teacher.account.password.update'), [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response
            ->assertRedirect(route('teacher.account.password.edit'))
            ->assertSessionHas('success');
        $this->assertTrue(Hash::check('new-password', $teacher->fresh()->password));
    }

    public function test_teacher_cannot_change_password_without_the_correct_current_password(): void
    {
        $teacher = $this->teacherUser('old-password');

        $response = $this->from(route('teacher.account.password.edit'))
            ->actingAs($teacher)
            ->put(route('teacher.account.password.update'), [
                'current_password' => 'incorrect-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertRedirect(route('teacher.account.password.edit'))
            ->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('old-password', $teacher->fresh()->password));
    }

    public function test_only_teachers_can_access_password_settings(): void
    {
        Role::findOrCreate('student');
        $student = User::factory()->create();
        $student->assignRole('student');

        $this->actingAs($student)
            ->get(route('teacher.account.password.edit'))
            ->assertForbidden();
    }

    private function teacherUser(string $password): User
    {
        Role::findOrCreate('teacher');
        $teacher = User::factory()->create(['password' => $password]);
        $teacher->assignRole('teacher');

        return $teacher;
    }
}
