<?php

namespace App\Http\Requests\Admin;

use App\Enums\StudyTrack;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterTeacherEvaluationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole("admin") ?? false;
    }

    public function rules(): array
    {
        return [
            "date"           => ["nullable", "date"],
            "teacher_id"     => ["nullable", "integer", "exists:teachers,id"],
            "grade_level_id" => ["nullable", "integer", "exists:grade_levels,id"],
            "track"          => ["nullable", Rule::in(StudyTrack::values())],
            "subject_id"     => ["nullable", "integer", "exists:subjects,id"],
            "student_id"     => ["nullable", "integer", "exists:students,id"],
            "attendance"     => ["nullable", Rule::in(["present", "absent", "late"])],
            "interaction"    => ["nullable", Rule::in(["engaged", "not_engaged"])],
            "status"         => ["nullable", Rule::in(["all", "complete", "incomplete", "started", "not_started"])],
        ];
    }
}
