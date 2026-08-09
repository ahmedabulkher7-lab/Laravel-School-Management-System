@extends('layouts.admin')
@section('title', 'تعديل طالب')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">تعديل بيانات الطالب: {{ $student->full_name }}</h1>
        <div class="page-subtitle">تحديث المعلومات الشخصية أو الأكاديمية</div>
    </div>
    <a href="{{ route('admin.students.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-right"></i> عودة للقائمة
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($errors->any())
            <div class="alert-error" style="margin-bottom:1rem;">
                <i class="fas fa-exclamation-circle"></i>
                يرجى مراجعة البيانات المطلوبة قبل الحفظ.
            </div>
        @endif
        <form action="{{ route('admin.students.update', $student) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="section-title">بيانات الدخول</div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني <span style="color:#ef4444">*</span></label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $student->user->email) }}" required>
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">كلمة المرور (اتركها فارغة إذا لم ترد تغييرها)</label>
                    <input type="password" name="password" class="form-input">
                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="section-title">البيانات الشخصية والدراسية</div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">الاسم الرباعي <span style="color:#ef4444">*</span></label>
                    <input type="text" name="full_name" class="form-input" value="{{ old('full_name', $student->full_name) }}" required>
                    @error('full_name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">المرحلة الدراسية <span style="color:#ef4444">*</span></label>
                    <select name="grade_level_id" class="form-select" required>
                        <option value="">-- اختر المرحلة --</option>
                        @foreach($gradeLevels as $gl)
                            <option value="{{ $gl->id }}" {{ old('grade_level_id', $student->grade_level_id) == $gl->id ? 'selected' : '' }}>{{ $gl->name }}</option>
                        @endforeach
                    </select>
                    @error('grade_level_id') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">المسار الدراسي <span style="color:#ef4444">*</span></label>
                    <select name="track" class="form-select" required>
                        <option value="">-- اختر المسار --</option>
                        @foreach($tracks as $trackItem)
                            <option value="{{ $trackItem->value }}" {{ old('track', $student->track?->value ?? $student->gradeLevel?->track?->value) === $trackItem->value ? 'selected' : '' }}>
                                {{ $trackItem->value === 'arabic' ? 'عربي' : 'لغات' }}
                            </option>
                        @endforeach
                    </select>
                    @error('track') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الميلاد <span style="color:#ef4444">*</span></label>
                    <input type="date" name="date_of_birth" class="form-input" value="{{ old('date_of_birth', $student->date_of_birth->format('Y-m-d')) }}" required>
                    @error('date_of_birth') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الالتحاق <span style="color:#ef4444">*</span></label>
                    <input type="date" name="enrollment_date" class="form-input" value="{{ old('enrollment_date', $student->enrollment_date->format('Y-m-d')) }}" required>
                    @error('enrollment_date') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="section-title">بيانات التواصل</div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">اسم ولي الأمر <span style="color:#ef4444">*</span></label>
                    <input type="text" name="guardian_name" class="form-input" value="{{ old('guardian_name', $student->guardian_name) }}" required>
                    @error('guardian_name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">رقم هاتف ولي الأمر <span style="color:#ef4444">*</span></label>
                    <input type="text" name="guardian_phone" class="form-input" value="{{ old('guardian_phone', $student->guardian_phone) }}" required>
                    @error('guardian_phone') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">رقم هاتف الطالب (اختياري)</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $student->phone) }}">
                    @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="margin-top:1.5rem;text-align:left;">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> تحديث البيانات</button>
            </div>
        </form>
    </div>
</div>
@endsection
