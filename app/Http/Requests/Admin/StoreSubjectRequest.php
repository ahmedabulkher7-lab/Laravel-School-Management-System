<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole("admin") ?? false;
    }

    public function rules(): array
    {
        return [
            "name"              => ["required", "string", "max:255"],
            "name_ar"           => ["nullable", "string", "max:255"],
            "color"             => ["required", "string", "max:7"],
            "grade_level_ids"   => ["nullable", "array"],
            "grade_level_ids.*" => ["exists:grade_levels,id"],
        ];
    }

    public function messages(): array
    {
        return [
            "name.required"  => "اسم المادة مطلوب.",
            "color.required" => "لون المادة مطلوب.",
        ];
    }
}
