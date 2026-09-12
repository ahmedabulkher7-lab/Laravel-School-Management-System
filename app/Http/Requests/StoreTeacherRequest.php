<?php
namespace App\Http\Requests;

use App\Enums\StudyTrack;
use App\Models\GradeLevel;
use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasRole('admin'); }

    public function rules(): array
    {
        $teacherId = $this->route('teacher')?->user_id;
        return [
            'full_name'         => 'required|string|max:255',
            'track'             => ['required', Rule::in(StudyTrack::values())],
            'subject_ids'       => 'required|array|min:1',
            'subject_ids.*'     => 'exists:subjects,id',
            'grade_level_ids'   => 'required|array|min:1',
            'grade_level_ids.*' => 'exists:grade_levels,id',
            'phone'             => 'nullable|string|max:20',
            'email'             => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($teacherId),
            ],
            'password'          => $teacherId ? 'nullable|min:8' : 'required|min:8',
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->hasAny(['track', 'subject_ids', 'grade_level_ids'])) {
                return;
            }

            $allowedTracks = $this->allowedTracks();
            $gradeLevelIds = $this->input('grade_level_ids', []);
            $subjectIds = $this->input('subject_ids', []);

            $invalidGradeLevelExists = GradeLevel::query()
                ->whereIn('id', $gradeLevelIds)
                ->whereNotIn('track', $allowedTracks)
                ->exists();
            if ($invalidGradeLevelExists) {
                $validator->errors()->add(
                    'grade_level_ids',
                    'لا يمكن اختيار صف من مسار مختلف عن المسار المحدد للمعلم.'
                );
            }

            $availableSubjectIds = Subject::query()
                ->whereIn('id', $subjectIds)
                ->whereHas('gradeLevels', fn ($query) => $query->whereIn('grade_levels.track', $allowedTracks))
                ->pluck('id');
            if (collect($subjectIds)->diff($availableSubjectIds)->isNotEmpty()) {
                $validator->errors()->add(
                    'subject_ids',
                    'اختر مواد مرتبطة بالمسار المحدد للمعلم.'
                );
            }
        }];
    }

    /** @return list<string> */
    private function allowedTracks(): array
    {
        return $this->input('track') === 'both'
            ? StudyTrack::values()
            : [$this->input('track'), StudyTrack::Both->value];
    }

    public function messages(): array
    {
        return [
            'full_name.required'       => 'الاسم الكامل مطلوب',
            'track.required'           => 'يجب اختيار مسار تدريس المعلم',
            'subject_ids.required'     => 'يجب اختيار مادة دراسية واحدة على الأقل',
            'grade_level_ids.required' => 'يجب اختيار مرحلة دراسية واحدة على الأقل',
            'email.required'           => 'البريد الإلكتروني مطلوب',
            'email.unique'             => 'البريد الإلكتروني مسجل مسبقاً',
        ];
    }
}
