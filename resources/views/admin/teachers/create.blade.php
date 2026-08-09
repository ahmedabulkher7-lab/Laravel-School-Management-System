@extends('layouts.admin')
@section('title', 'إضافة معلم')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إضافة معلم جديد</h1>
        <div class="page-subtitle">تسجيل معلم جديد وتحديد المادة والمراحل</div>
    </div>
    <a href="{{ route('admin.teachers.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-right"></i> عودة للقائمة
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.teachers.store') }}" method="POST">
            @csrf

            <div class="section-title">بيانات الدخول</div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني <span style="color:#ef4444">*</span></label>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" required>
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">كلمة المرور <span style="color:#ef4444">*</span></label>
                    <input type="password" name="password" class="form-input" required>
                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="section-title">البيانات الشخصية</div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">الاسم الكامل <span style="color:#ef4444">*</span></label>
                    <input type="text" name="full_name" class="form-input" value="{{ old('full_name') }}" required>
                    @error('full_name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">رقم الهاتف (اختياري)</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone') }}">
                    @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="section-title">الاختصاص الأكاديمي</div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">المواد التي يدرسها <span style="color:#ef4444">*</span></label>
                    <div style="background:#f8fafc;border:1px solid rgba(71,85,105,0.6);border-radius:0.75rem;padding:1rem;display:flex;flex-wrap:wrap;gap:1rem;">
                        @php $assignedSubjects = old('subject_ids', []); @endphp
                        @foreach($subjects as $subj)
                        <label style="display:flex;align-items:center;gap:0.4rem;color:#475569;cursor:pointer;">
                            <input type="checkbox" name="subject_ids[]" value="{{ $subj->id }}"
                                {{ in_array($subj->id, $assignedSubjects) ? 'checked' : '' }}
                                style="accent-color:#0C7261;width:16px;height:16px;">
                            {{ $subj->name_ar ?? $subj->name }}
                        </label>
                        @endforeach
                    </div>
                    @error('subject_ids') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">المراحل الدراسية المصرح له بتدريسها <span style="color:#ef4444">*</span></label>

                    {{-- Track Filter Buttons --}}
                    <div style="display:flex;gap:0.5rem;margin-bottom:0.75rem;flex-wrap:wrap;">
                        <button type="button" onclick="filterGrades('all')" id="filter-all"
                                style="padding:0.3rem 0.9rem;border-radius:2rem;border:2px solid #0C7261;
                                       background:#0C7261;color:white;font-size:0.8rem;font-weight:600;cursor:pointer;
                                       font-family:inherit;transition:all 0.2s;">
                            🔍 الكل
                        </button>
                        @foreach($tracks as $trackItem)
                        <button type="button" onclick="filterGrades('{{ $trackItem->value }}')" id="filter-{{ $trackItem->value }}"
                                style="padding:0.3rem 0.9rem;border-radius:2rem;border:2px solid #cbd5e1;
                                       background:white;color:#475569;font-size:0.8rem;font-weight:600;cursor:pointer;
                                       font-family:inherit;transition:all 0.2s;">
                            {{ $trackItem->value === 'arabic' ? '📚' : '🌐' }} {{ $trackItem->label() }}
                        </button>
                        @endforeach
                    </div>

                    <div id="grade-levels-container" style="background:#f8fafc;border:1px solid rgba(71,85,105,0.6);border-radius:0.75rem;padding:1rem;">
                        @foreach($gradeLevels as $gl)
                        <label class="grade-label"
                               data-track="{{ $gl->track->value ?? $gl->track }}"
                               style="display:flex;align-items:center;gap:0.4rem;color:#475569;cursor:pointer;
                                      padding:0.3rem 0.5rem;border-radius:0.5rem;transition:background 0.15s;
                                      margin-bottom:0.25rem;">
                            <input type="checkbox" name="grade_level_ids[]" value="{{ $gl->id }}"
                                {{ in_array($gl->id, old('grade_level_ids', [])) ? 'checked' : '' }}
                                style="accent-color:#0C7261;width:16px;height:16px;flex-shrink:0;">
                            <span>{{ $gl->name }}</span>
                            <span style="font-size:0.7rem;color:#94a3b8;margin-right:auto;
                                         background:{{ ($gl->track->value ?? $gl->track) === 'arabic' ? 'rgba(5,150,105,0.1)' : 'rgba(37,99,235,0.1)' }};
                                         color:{{ ($gl->track->value ?? $gl->track) === 'arabic' ? '#065f46' : '#1e40af' }};
                                         padding:0.1rem 0.5rem;border-radius:2rem;">
                                {{ ($gl->track->value ?? $gl->track) === 'arabic' ? 'عربي' : 'لغات' }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                    @error('grade_level_ids') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="margin-top:1.5rem;text-align:left;">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> حفظ البيانات</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function filterGrades(track) {
    const labels = document.querySelectorAll('.grade-label');
    const buttons = document.querySelectorAll('[id^="filter-"]');

    // Reset all buttons
    buttons.forEach(btn => {
        btn.style.background = 'white';
        btn.style.color = '#475569';
        btn.style.borderColor = '#cbd5e1';
    });

    // Activate selected button
    const activeBtn = document.getElementById('filter-' + track);
    if (activeBtn) {
        activeBtn.style.background = '#0C7261';
        activeBtn.style.color = 'white';
        activeBtn.style.borderColor = '#0C7261';
    }

    labels.forEach(label => {
        if (track === 'all' || label.dataset.track === track) {
            label.style.display = 'flex';
        } else {
            label.style.display = 'none';
        }
    });
}
</script>
@endpush
@endsection
