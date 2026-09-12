@extends('layouts.teacher')

@section('title', 'إعدادات الحساب')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إعدادات الحساب</h1>
        <div class="page-subtitle">غيّر كلمة المرور الخاصة بحسابك بأمان.</div>
    </div>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-key"></i> تغيير كلمة المرور</span>
    </div>
    <div class="card-body">
        <div style="display:flex;gap:0.7rem;align-items:flex-start;background:rgba(12,114,97,0.08);border-radius:0.75rem;padding:0.85rem 1rem;margin-bottom:1.5rem;color:#0C7261;font-size:0.86rem;">
            <i class="fas fa-shield-alt" style="margin-top:0.15rem;"></i>
            <span>لحماية حسابك، أدخل كلمة المرور الحالية ثم اختر كلمة جديدة لا تقل عن 8 أحرف.</span>
        </div>

        <form method="POST" action="{{ route('teacher.account.password.update') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="current_password">كلمة المرور الحالية</label>
                <input id="current_password" name="current_password" type="password" class="form-input"
                       autocomplete="current-password" required>
                @error('current_password') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">كلمة المرور الجديدة</label>
                <input id="password" name="password" type="password" class="form-input"
                       autocomplete="new-password" minlength="8" required>
                @error('password') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">تأكيد كلمة المرور الجديدة</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-input"
                       autocomplete="new-password" minlength="8" required>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> حفظ كلمة المرور الجديدة
            </button>
        </form>
    </div>
</div>
@endsection
