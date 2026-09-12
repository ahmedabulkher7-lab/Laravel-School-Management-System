<?php

namespace Tests\Feature;

use App\Livewire\Teacher\DailyProgressLog;
use App\Models\GradeLevel;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeacherProgressSubjectSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_selects_a_subject_before_the_available_tracks_and_grade_levels(): void
    {
        [$teacherUser, $teacher, $arabicGrade, $languagesGrade, $math, $english] = $this->teacherSetup();
        $this->createStudent('طالب عربي', $arabicGrade);

        $this->actingAs($teacherUser)
            ->get(route('teacher.progress.log'))
            ->assertOk()
            ->assertSee('اختر المادة')
            ->assertSee('الرياضيات')
            ->assertSee('اللغة الإنجليزية');

        $this->actingAs($teacherUser)
            ->get(route('teacher.progress.log', ['subject_id' => $math->id]))
            ->assertOk()
            ->assertSee('اختر المسار الدراسي')
            ->assertSee('عربي')
            ->assertSee('لغات');

        $this->actingAs($teacherUser)
            ->get(route('teacher.progress.log', ['subject_id' => $english->id]))
            ->assertOk()
            ->assertSee('اختر المسار الدراسي')
            ->assertSee('عربي')
            ->assertDontSee('لغات');

        $this->actingAs($teacherUser)
            ->get(route('teacher.progress.log', [
                'subject_id' => $math->id,
                'track' => 'arabic',
                'grade_level_id' => $arabicGrade->id,
            ]))
            ->assertOk()
            ->assertSee('الرياضيات — مسار عربي — الصف الأول عربي');
    }

    public function test_daily_progress_is_saved_for_the_subject_selected_by_the_teacher(): void
    {
        [$teacherUser, $teacher, $arabicGrade, , $math] = $this->teacherSetup();
        $student = $this->createStudent('طالب عربي', $arabicGrade);

        Livewire::actingAs($teacherUser)
            ->test(DailyProgressLog::class, [
                'studentId' => $student->id,
                'teacherId' => $teacher->id,
                'subjectId' => $math->id,
            ])
            ->set('date', '2026-08-10')
            ->set('score', 9)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('daily_progress', [
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $math->id,
            'date' => '2026-08-10',
            'score' => 9,
        ]);
    }

    /** @return array{User, Teacher, GradeLevel, GradeLevel, Subject, Subject} */
    private function teacherSetup(): array
    {
        Role::findOrCreate('teacher');

        $teacherUser = User::factory()->create(['name' => 'مدرس تجريبي']);
        $teacherUser->assignRole('teacher');
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'full_name' => 'مدرس تجريبي']);

        $arabicGrade = GradeLevel::create(['name' => 'الصف الأول عربي', 'order' => 1, 'track' => 'arabic']);
        $languagesGrade = GradeLevel::create(['name' => 'Grade 1 Languages', 'order' => 1, 'track' => 'languages']);
        $math = Subject::create(['name' => 'Mathematics', 'name_ar' => 'الرياضيات']);
        $english = Subject::create(['name' => 'English', 'name_ar' => 'اللغة الإنجليزية']);

        $arabicGrade->subjects()->attach([$math->id, $english->id]);
        $languagesGrade->subjects()->attach($math->id);
        $teacher->subjects()->attach([$math->id, $english->id]);
        $teacher->gradeLevels()->attach([$arabicGrade->id, $languagesGrade->id]);

        return [$teacherUser, $teacher, $arabicGrade, $languagesGrade, $math, $english];
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
            'track' => $gradeLevel->track->value,
        ]);
    }
}
