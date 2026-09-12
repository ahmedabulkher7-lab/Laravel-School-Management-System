<?php

namespace App\Http\Requests;

use App\Enums\StudyTrack;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGradeLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole("admin");
    }

    public function rules(): array
    {
        $gradeLevelId = $this->route("grade_level")?->id;

        return [
            "name" => [
                "required",
                "string",
                "max:100",
                Rule::unique("grade_levels", "name")
                    ->ignore($gradeLevelId)
                    ->where("track", $this->input("track")),
            ],
            "order" => ["required", "integer", "min:1"],
            "track" => ["required", Rule::enum(StudyTrack::class)],
        ];
    }

    public function messages(): array
    {
        return [
            "name.required"  => "اسم المرحلة مطلوب.",
            "name.unique"    => "هذه المرحلة موجودة مسبقاً في نفس المسار.",
            "order.required" => "ترتيب المرحلة مطلوب.",
            "track.required" => "المسار الدراسي مطلوب.",
        ];
    }
}
