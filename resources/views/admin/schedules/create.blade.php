@extends('layouts.admin')
@section('title', 'إضافة حصة')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إضافة حصة للجدول الدراسي</h1>
    </div>
    <a href="{{ route('admin.schedules.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-right"></i> عودة
    </a>
</div>

<div class="card" style="max-width:800px;">
    <div class="card-body">
        <form action="{{ route('admin.schedules.store') }}" method="POST">
            @csrf
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">المرحلة الدراسية <span style="color:#ef4444">*</span></label>
                    <select name="grade_level_id" class="form-select" required>
                        <option value="">-- اختر المرحلة --</option>
                        @foreach($gradeLevels as $gl)
                            <option value="{{ $gl->id }}" {{ old('grade_level_id') == $gl->id ? 'selected' : '' }}>{{ $gl->name }}</option>
                        @endforeach
                    </select>
                    @error('grade_level_id') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">اليوم <span style="color:#ef4444">*</span></label>
                    <select name="day_of_week" class="form-select" required>
                        <option value="">-- اختر اليوم --</option>
                        <option value="0" {{ old('day_of_week') == '0' ? 'selected' : '' }}>الأحد</option>
                        <option value="1" {{ old('day_of_week') == '1' ? 'selected' : '' }}>الإثنين</option>
                        <option value="2" {{ old('day_of_week') == '2' ? 'selected' : '' }}>الثلاثاء</option>
                        <option value="3" {{ old('day_of_week') == '3' ? 'selected' : '' }}>الأربعاء</option>
                        <option value="4" {{ old('day_of_week') == '4' ? 'selected' : '' }}>الخميس</option>
                        <option value="5" {{ old('day_of_week') == '5' ? 'selected' : '' }}>الجمعة</option>
                        <option value="6" {{ old('day_of_week') == '6' ? 'selected' : '' }}>السبت</option>
                    </select>
                    @error('day_of_week') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">المعلم والمادة <span style="color:#ef4444">*</span></label>
                    <select name="teacher_id" id="teacher_select" class="form-select" required onchange="updateSubjects()">
                        <option value="">-- اختر المعلم --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" data-subject="{{ $teacher->subject_id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->full_name }} ({{ $teacher->subject->name_ar ?? $teacher->subject->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <input type="hidden" name="subject_id" id="subject_id" value="{{ old('subject_id') }}">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">وقت البداية <span style="color:#ef4444">*</span></label>
                    <input type="time" name="start_time" class="form-input" value="{{ old('start_time') }}" required>
                    @error('start_time') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">وقت النهاية <span style="color:#ef4444">*</span></label>
                    <input type="time" name="end_time" class="form-input" value="{{ old('end_time') }}" required>
                    @error('end_time') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="margin-top:1.5rem;text-align:left;">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> إضافة الحصة</button>
            </div>
        </form>
        <script>
            function updateSubjects() {
                const select = document.getElementById('teacher_select');
                if(select.selectedIndex > 0) {
                    const subjectId = select.options[select.selectedIndex].getAttribute('data-subject');
                    document.getElementById('subject_id').value = subjectId;
                }
            }
            // run on load in case of validation error old value
            document.addEventListener('DOMContentLoaded', updateSubjects);
        </script>
    </div>
</div>
@endsection
