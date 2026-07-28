<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasRole('admin'); }

    public function rules(): array
    {
        $studentId = $this->route('student')?->id;
        $userId = $this->route('student')?->user_id;
        return [
            'full_name'       => 'required|string|max:255',
            'date_of_birth'   => 'required|date|before:today',
            'grade_level_id'  => 'required|exists:grade_levels,id',
            'guardian_name'   => 'required|string|max:255',
            'guardian_phone'  => 'required|string|max:20',
            'phone'           => 'nullable|string|max:20',
            'enrollment_date' => 'required|date',
            'email'           => "required|email|unique:users,email,{$userId},id",
            'password'        => $studentId ? 'nullable|min:8' : 'required|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'      => 'الاسم الكامل مطلوب',
            'date_of_birth.required'  => 'تاريخ الميلاد مطلوب',
            'grade_level_id.required' => 'المرحلة الدراسية مطلوبة',
            'guardian_name.required'  => 'اسم ولي الأمر مطلوب',
            'guardian_phone.required' => 'رقم هاتف ولي الأمر مطلوب',
            'email.required'          => 'البريد الإلكتروني مطلوب',
            'email.unique'            => 'البريد الإلكتروني مسجل مسبقاً',
            'password.required'       => 'كلمة المرور مطلوبة',
            'password.min'            => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
        ];
    }
}
