<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasRole('admin'); }

    public function rules(): array
    {
        $teacherId = $this->route('teacher')?->user_id;
        return [
            'full_name'         => 'required|string|max:255',
            'subject_id'        => 'required|exists:subjects,id',
            'grade_level_ids'   => 'required|array|min:1',
            'grade_level_ids.*' => 'exists:grade_levels,id',
            'phone'             => 'nullable|string|max:20',
            'email'             => "required|email|unique:users,email,{$teacherId}",
            'password'          => $teacherId ? 'nullable|min:8' : 'required|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'       => 'الاسم الكامل مطلوب',
            'subject_id.required'      => 'المادة الدراسية مطلوبة',
            'grade_level_ids.required' => 'يجب اختيار مرحلة دراسية واحدة على الأقل',
            'email.required'           => 'البريد الإلكتروني مطلوب',
            'email.unique'             => 'البريد الإلكتروني مسجل مسبقاً',
        ];
    }
}
