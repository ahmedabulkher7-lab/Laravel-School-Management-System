@extends('layouts.admin')
@section('title', 'جدول الحصص')
@section('content')
<div class="page-header"><div><h1 class="page-title">جدول الحصص الأسبوعي</h1><div class="page-subtitle">جدول مستقل لكل صف. اختر المادة في الخلية ليُحدّد المدرس المسند لها تلقائيًا.</div></div></div>
<div class="card" style="margin-bottom:1.5rem;"><div class="card-body"><form method="GET" class="grid-2"><div class="form-group"><label class="form-label">المسار الدراسي</label><select name="track" class="form-select" onchange="this.form.submit()"><option value="">اختر المسار</option><option value="arabic" @selected($track === 'arabic')>عربي</option><option value="languages" @selected($track === 'languages')>لغات</option><option value="all" @selected($track === 'all')>عربي ولغات</option></select></div><div class="form-group"><label class="form-label">الصف الدراسي</label><select name="grade_level_id" class="form-select" onchange="this.form.submit()" @disabled(!$track)><option value="">اختر الصف</option>@foreach($gradeLevels->when($track && $track !== 'all', fn ($items) => $items->filter(fn ($grade) => $grade->track->value === $track)) as $grade)<option value="{{ $grade->id }}" @selected($selectedGrade?->id === $grade->id)>{{ $grade->name }}</option>@endforeach</select></div></form></div></div>

@if($selectedGrade)
<form method="POST" action="{{ route('admin.schedules.grid.save') }}" id="schedule-grid-form">@csrf<input type="hidden" name="grade_level_id" value="{{ $selectedGrade->id }}"><div class="card" style="overflow:auto;"><div class="card-body" style="min-width:1040px;"><div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;"><div><strong style="font-size:1.1rem;color:#0C7261;">{{ $selectedGrade->name }}</strong><span class="badge badge-blue" style="margin-right:.5rem;">{{ $selectedGrade->track->value === 'arabic' ? 'عربي' : 'لغات' }}</span></div><button type="submit" class="btn-primary"><i class="fas fa-save"></i> حفظ جدول الصف</button></div>@error('slots')<div class="alert-error">{{ $message }}</div>@enderror @error('cells')<div class="alert-error">{{ $message }}</div>@enderror
<table class="data-table" style="table-layout:fixed;"><thead><tr><th style="width:150px;">وقت الحصة</th><th>الأحد</th><th>الإثنين</th><th>الثلاثاء</th><th>الأربعاء</th><th>الخميس</th></tr></thead><tbody>
@foreach($slots as $slotIndex => $slot)<tr><td><input type="time" name="slots[{{ $slotIndex }}][start_time]" value="{{ old("slots.$slotIndex.start_time", $slot['start_time']) }}" class="form-input" style="padding:.35rem;"><div style="text-align:center;color:#64748b;margin:.2rem 0;">إلى</div><input type="time" name="slots[{{ $slotIndex }}][end_time]" value="{{ old("slots.$slotIndex.end_time", $slot['end_time']) }}" class="form-input" style="padding:.35rem;"></td>
@foreach(range(0, 4) as $day)
@php
    $key = $day . '-' . $slot['start_time'] . '-' . $slot['end_time'];
    $lesson = $scheduleGrid->get($key);
    $cellKey = $day . '_' . $slotIndex;
    $subjectId = old("cells.$cellKey.subject_id", $lesson?->subject_id);
    $teacherId = old("cells.$cellKey.teacher_id", $lesson?->teacher_id);
@endphp
<td style="vertical-align:top;"><select class="form-select subject-choice" name="cells[{{ $cellKey }}][subject_id]" data-cell="{{ $cellKey }}" style="font-size:.82rem;padding:.45rem;"><option value="">— فارغ / استراحة —</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected((int) $subjectId === $subject->id)>{{ $subject->name_ar ?? $subject->name }}</option>@endforeach</select><input type="hidden" class="teacher-id" name="cells[{{ $cellKey }}][teacher_id]" value="{{ $teacherId }}"><div class="teacher-name" data-cell="{{ $cellKey }}" style="font-size:.75rem;color:#64748b;margin-top:.45rem;min-height:1.2rem;">{{ $lesson?->teacher?->full_name }}</div></td>
@endforeach</tr>@endforeach
</tbody></table></div></div></form>
@else
<div class="card" style="text-align:center;padding:2.5rem;color:#64748b;">اختر المسار ثم الصف لعرض جدول هذا الصف وتعديله.</div>
@endif
@endsection
@push('scripts')
<script>
const teacherOptions = @json($teacherOptions);
document.querySelectorAll('.subject-choice').forEach((select) => {
  const refreshTeacher = () => {
    const cell = select.dataset.cell, input = document.querySelector('.teacher-id[name="cells[' + cell + '][teacher_id]"]'), label = document.querySelector('.teacher-name[data-cell="' + cell + '"]'), options = teacherOptions[select.value] || [];
    if (!select.value) { input.value = ''; label.textContent = ''; return; }
    if (options.length === 1) { input.value = options[0].id; label.textContent = 'المدرس: ' + options[0].full_name; return; }
    input.value = ''; label.textContent = options.length ? 'يوجد أكثر من مدرس لهذه المادة؛ راجع إسناد المدرسين.' : 'لا يوجد مدرس مسند لهذه المادة والصف.';
  };
  select.addEventListener('change', refreshTeacher);
  if (!document.querySelector('.teacher-name[data-cell="' + select.dataset.cell + '"]').textContent.trim()) refreshTeacher();
});
</script>
@endpush
