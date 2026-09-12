<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can("create", \App\Models\Schedule::class) ?? false;
    }

    public function rules(): array
    {
        return [
            "grade_level_id" => ["required", "exists:grade_levels,id"],
            "subject_id"     => ["required", "exists:subjects,id"],
            "teacher_id"     => ["required", "exists:teachers,id"],
            "day_of_week"    => ["required", "integer", "between:0,6"],
            "start_time"     => ["required", "date_format:H:i"],
            "end_time"       => ["required", "date_format:H:i", "after:start_time"],
        ];
    }

    public function messages(): array
    {
        return [
            "grade_level_id.required" => "الصف الدراسي مطلوب.",
            "grade_level_id.exists"   => "الصف الدراسي المحدد غير موجود.",
            "subject_id.required"     => "المادة الدراسية مطلوبة.",
            "subject_id.exists"       => "المادة المحددة غير موجودة.",
            "teacher_id.required"     => "المعلم مطلوب.",
            "teacher_id.exists"       => "المعلم المحدد غير موجود.",
            "day_of_week.required"    => "اليوم مطلوب.",
            "start_time.required"     => "وقت البداية مطلوب.",
            "end_time.required"       => "وقت النهاية مطلوب.",
            "end_time.after"          => "وقت النهاية يجب أن يكون بعد وقت البداية.",
        ];
    }
}
