@extends('layouts.admin')
@section('title', 'تعديل معلم')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">تعديل المعلم: {{ $teacher->full_name }}</h1>
        <div class="page-subtitle">تحديث البيانات أو الصلاحيات الأكاديمية</div>
    </div>
    <a href="{{ route('admin.teachers.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-right"></i> عودة للقائمة
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="section-title">بيانات الدخول</div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني <span style="color:#ef4444">*</span></label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $teacher->user->email) }}" required>
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">كلمة المرور (اتركها فارغة إذا لم ترد تغييرها)</label>
                    <input type="password" name="password" class="form-input">
                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="section-title">البيانات الشخصية</div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">الاسم الكامل <span style="color:#ef4444">*</span></label>
                    <input type="text" name="full_name" class="form-input" value="{{ old('full_name', $teacher->full_name) }}" required>
                    @error('full_name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">رقم الهاتف (اختياري)</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $teacher->phone) }}">
                    @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="section-title">الاختصاص الأكاديمي</div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">المادة التي يدرسها <span style="color:#ef4444">*</span></label>
                    <select name="subject_id" class="form-select" required>
                        <option value="">-- اختر المادة --</option>
                        @foreach($subjects as $subj)
                            <option value="{{ $subj->id }}" {{ old('subject_id', $teacher->subject_id) == $subj->id ? 'selected' : '' }}>
                                {{ $subj->name_ar ?? $subj->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">المراحل الدراسية المصرح له بتدريسها <span style="color:#ef4444">*</span></label>
                    <div style="background:#f8fafc;border:1px solid rgba(71,85,105,0.6);border-radius:0.75rem;padding:1rem;display:flex;flex-wrap:wrap;gap:1rem;">
                        @php $assignedLevels = old('grade_level_ids', $teacher->gradeLevels->pluck('id')->toArray()); @endphp
                        @foreach($gradeLevels as $gl)
                        <label style="display:flex;align-items:center;gap:0.4rem;color:#475569;cursor:pointer;">
                            <input type="checkbox" name="grade_level_ids[]" value="{{ $gl->id }}"
                                {{ in_array($gl->id, $assignedLevels) ? 'checked' : '' }}
                                style="accent-color:#0C7261;width:16px;height:16px;">
                            {{ $gl->name }}
                        </label>
                        @endforeach
                    </div>
                    @error('grade_level_ids') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="margin-top:1.5rem;text-align:left;">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> تحديث البيانات</button>
            </div>
        </form>
    </div>
</div>
@endsection
