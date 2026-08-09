<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveWeeklyPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('teacher') ?? false;
    }

    public function rules(): array
    {
        return [
            'week_start' => ['required', 'date'],
            'plans' => ['required', 'array', 'min:1'],
            'plans.*.subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'plans.*.class_work' => ['nullable', 'string', 'max:2000'],
            'plans.*.homework' => ['nullable', 'string', 'max:2000'],
            'plans.*.online_games' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
