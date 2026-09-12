<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WeeklyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole("admin") ?? false;
    }

    public function rules(): array
    {
        return [
            "week_start" => ["required", "date"],
        ];
    }

    public function messages(): array
    {
        return [
            "week_start.required" => "تاريخ بداية الأسبوع مطلوب",
            "week_start.date"     => "التاريخ غير صالح",
        ];
    }
}
