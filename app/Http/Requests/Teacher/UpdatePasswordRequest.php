<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole("teacher") ?? false;
    }

    public function rules(): array
    {
        return [
            "current_password" => ["required", "current_password"],
            "password"         => ["required", "string", "min:8", "confirmed", "different:current_password"],
        ];
    }

    public function messages(): array
    {
        return [
            "current_password.required"         => "أدخل كلمة المرور الحالية.",
            "current_password.current_password" => "كلمة المرور الحالية غير صحيحة.",
            "password.required"                 => "أدخل كلمة المرور الجديدة.",
            "password.min"                      => "يجب أن تتكون كلمة المرور الجديدة من 8 أحرف على الأقل.",
            "password.confirmed"                => "تأكيد كلمة المرور الجديدة غير متطابق.",
            "password.different"                => "اختر كلمة مرور جديدة مختلفة عن الحالية.",
        ];
    }
}
