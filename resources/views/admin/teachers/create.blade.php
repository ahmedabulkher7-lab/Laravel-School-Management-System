@extends('layouts.admin')
@section('title', 'إضافة معلم')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إضافة معلم جديد</h1>
        <div class="page-subtitle">حدد مسار التدريس، ثم المواد والصفوف التابعة له</div>
    </div>
    <a href="{{ route('admin.teachers.index') }}" class="btn-secondary"><i class="fas fa-arrow-right"></i> عودة للقائمة</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.teachers.store') }}" method="POST" data-teacher-track-form>
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

            @php $selectedTeachingTrack = old('track', 'arabic'); @endphp
            <div class="section-title">الاختصاص الأكاديمي</div>
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">مسار تدريس المعلم <span style="color:#ef4444">*</span></label>
                <p style="margin:.25rem 0 .85rem;color:#64748b;font-size:.82rem;">اختيارك هنا يحدد المواد والصفوف التي يمكن إسنادها للمعلم.</p>
                <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                    @foreach(['arabic' => ['📚', 'عربي'], 'languages' => ['🌐', 'لغات'], 'both' => ['📚🌐', 'عربي ولغات']] as $value => [$icon, $label])
                        <label style="display:flex;align-items:center;gap:.5rem;padding:.7rem 1rem;border:1px solid #cbd5e1;border-radius:.7rem;cursor:pointer;background:#f8fafc;">
                            <input type="radio" name="track" value="{{ $value }}" data-teaching-track {{ $selectedTeachingTrack === $value ? 'checked' : '' }} style="accent-color:#0C7261;">
                            <span>{{ $icon }} {{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('track') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">المواد التي يدرسها <span style="color:#ef4444">*</span></label>
                    <div style="background:#f8fafc;border:1px solid rgba(71,85,105,.6);border-radius:.75rem;padding:1rem;display:flex;flex-wrap:wrap;gap:.75rem;">
                        @php $assignedSubjects = old('subject_ids', []); @endphp
                        @foreach($subjects as $subject)
                            @php $subjectTracks = $subject->gradeLevels->pluck('track')->map(fn ($track) => $track->value)->unique()->join(','); @endphp
                            <label data-subject-option data-tracks="{{ $subjectTracks }}" style="display:flex;align-items:center;gap:.4rem;color:#475569;cursor:pointer;">
                                <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" {{ in_array($subject->id, $assignedSubjects) ? 'checked' : '' }} style="accent-color:#0C7261;width:16px;height:16px;">
                                {{ $subject->name_ar ?? $subject->name }}
                            </label>
                        @endforeach
                    </div>
                    @error('subject_ids') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">الصفوف المصرح له بتدريسها <span style="color:#ef4444">*</span></label>
                    <div style="background:#f8fafc;border:1px solid rgba(71,85,105,.6);border-radius:.75rem;padding:1rem;">
                        @foreach($gradeLevels as $gradeLevel)
                            @php $gradeTrack = $gradeLevel->track->value; @endphp
                            <label data-grade-option data-track="{{ $gradeTrack }}" style="display:flex;align-items:center;gap:.4rem;color:#475569;cursor:pointer;padding:.3rem .5rem;border-radius:.5rem;margin-bottom:.25rem;">
                                <input type="checkbox" name="grade_level_ids[]" value="{{ $gradeLevel->id }}" {{ in_array($gradeLevel->id, old('grade_level_ids', [])) ? 'checked' : '' }} style="accent-color:#0C7261;width:16px;height:16px;flex-shrink:0;">
                                <span>{{ $gradeLevel->name }}</span>
                                <span style="font-size:.7rem;color:{{ $gradeTrack === 'arabic' ? '#065f46' : ($gradeTrack === 'languages' ? '#1e40af' : '#7e22ce') }};margin-right:auto;">{{ $gradeLevel->track->label() }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('grade_level_ids') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="margin-top:1.5rem;text-align:left;"><button type="submit" class="btn-primary"><i class="fas fa-save"></i> حفظ البيانات</button></div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-teacher-track-form]');
    if (!form) return;

    const allowedTracks = () => {
        const value = form.querySelector('[data-teaching-track]:checked')?.value;
        return value === 'both' ? ['arabic', 'languages'] : [value];
    };
    const updateOptions = () => {
        const allowed = allowedTracks();
        form.querySelectorAll('[data-subject-option]').forEach((option) => {
            const matches = option.dataset.tracks.split(',').some((track) => track === 'both' || allowed.includes(track));
            option.hidden = !matches;
            option.querySelector('input').disabled = !matches;
            if (!matches) option.querySelector('input').checked = false;
        });
        form.querySelectorAll('[data-grade-option]').forEach((option) => {
            const matches = option.dataset.track === 'both' || allowed.includes(option.dataset.track);
            option.hidden = !matches;
            option.querySelector('input').disabled = !matches;
            if (!matches) option.querySelector('input').checked = false;
        });
    };

    form.querySelectorAll('[data-teaching-track]').forEach((input) => input.addEventListener('change', updateOptions));
    updateOptions();
});
</script>
@endpush
