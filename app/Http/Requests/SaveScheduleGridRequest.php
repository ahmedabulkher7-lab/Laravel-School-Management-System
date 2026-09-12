<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveScheduleGridRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can("create", \App\Models\Schedule::class);
    }

    public function rules(): array
    {
        return [
            "grade_level_id"     => ["required", "integer", "exists:grade_levels,id"],
            "slots"              => ["required", "array", "min:1"],
            "slots.*.start_time" => ["required", "date_format:H:i"],
            "slots.*.end_time"   => ["required", "date_format:H:i"],
            "cells"              => ["nullable", "array"],
            "cells.*.subject_id" => ["nullable", "integer", "exists:subjects,id"],
            "cells.*.teacher_id" => ["nullable", "integer", "exists:teachers,id"],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $slots = $this->input("slots", []);
            foreach ($slots as $slot) {
                if (isset($slot["start_time"], $slot["end_time"]) && $slot["end_time"] <= $slot["start_time"]) {
                    $validator->errors()->add("slots", "وقت نهاية الحصة يجب أن يكون بعد وقت البداية.");
                    break;
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            "grade_level_id.required" => "الصف الدراسي مطلوب.",
            "grade_level_id.exists"   => "الصف الدراسي المحدد غير موجود.",
            "slots.required"          => "يجب تحديد الفترات الزمنية للحصص.",
        ];
    }
}
