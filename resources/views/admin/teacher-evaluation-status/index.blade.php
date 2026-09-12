@extends('layouts.admin')
@section('title', 'متابعة تقييمات المدرسين')

@push('styles')
<style>
    .teacher-status-filter-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:1rem; }
    .teacher-status-summary { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin:0 0 1.5rem; }
    .teacher-status-card { border:1px solid #d9e7e1; border-radius:1rem; overflow:hidden; background:#fff; box-shadow:0 3px 12px rgba(12,114,97,.04); }
    .teacher-status-card.is-complete { border-color:rgba(12,114,97,.45); }
    .teacher-status-card.is-incomplete { border-color:rgba(245,158,11,.42); }
    .teacher-status-card-head { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; padding:1.15rem 1.25rem; background:#f2f8f5; border-bottom:1px solid #d9e7e1; }
    .teacher-status-card.is-incomplete .teacher-status-card-head { background:#fffaf0; }
    .teacher-status-metrics { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; margin:1.15rem 1.25rem; }
    .teacher-status-metric { background:#f8faf9; border-radius:.65rem; padding:.8rem; text-align:center; }
    .teacher-status-metric strong { display:block; color:#0C7261; font-size:1.25rem; line-height:1.2; }
    .teacher-status-metric span { color:#64748b; font-size:.72rem; font-weight:700; }
    .teacher-status-lists { display:grid; grid-template-columns:1fr 1fr; gap:1rem; padding:0 1.25rem 1.25rem; }
    .teacher-status-list { border:1px solid #e2e8f0; border-radius:.7rem; overflow:hidden; }
    .teacher-status-list-title { display:flex; justify-content:space-between; align-items:center; padding:.65rem .8rem; font-size:.78rem; font-weight:800; }
    .teacher-status-list-title.logged { background:#eaf7f1; color:#08725e; }
    .teacher-status-list-title.remaining { background:#fff5e7; color:#a16207; }
    .teacher-status-list-body { max-height:164px; overflow:auto; padding:.35rem .8rem; }
    .teacher-status-student { border-bottom:1px solid #edf1ef; color:#334155; font-size:.79rem; padding:.48rem 0; }
    .teacher-status-student:last-child { border-bottom:0; }
    .teacher-status-empty { color:#94a3b8; font-size:.78rem; padding:.7rem 0; text-align:center; }
    @media (max-width:900px) { .teacher-status-summary { grid-template-columns:repeat(2,1fr); } }
    @media (max-width:640px) { .teacher-status-summary,.teacher-status-lists { grid-template-columns:1fr; } .teacher-status-card-head { flex-direction:column; } }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">متابعة تقييمات المدرسين</h1>
        <div class="page-subtitle">اعرف من سجّل تقييمات طلابه ومن ما زال لديه طلاب متبقون في اليوم المختار.</div>
    </div>
    <div style="font-size:.85rem;color:#475569;"><i class="fas fa-calendar-day"></i> {{ $date->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}</div>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.teacher-evaluation-status.index') }}">
            <div class="teacher-status-filter-grid">
                <div class="form-group"><label class="form-label" for="date">التاريخ</label><input id="date" type="date" name="date" class="form-input" value="{{ $date->toDateString() }}"></div>
                <div class="form-group"><label class="form-label" for="teacher_id">المدرس</label><select id="teacher_id" name="teacher_id" class="form-select"><option value="">كل المدرسين</option>@foreach($filterTeachers as $filterTeacher)<option value="{{ $filterTeacher->id }}" {{ $teacherId == $filterTeacher->id ? 'selected' : '' }}>{{ $filterTeacher->full_name }}</option>@endforeach</select></div>
                <div class="form-group"><label class="form-label" for="track">المسار</label><select id="track" name="track" class="form-select"><option value="">كل المسارات</option>@foreach($tracks as $trackItem)<option value="{{ $trackItem->value }}" {{ $track === $trackItem->value ? 'selected' : '' }}>{{ $trackItem->value === 'arabic' ? 'عربي' : 'لغات' }}</option>@endforeach</select></div>
                <div class="form-group"><label class="form-label" for="grade_level_id">الصف</label><select id="grade_level_id" name="grade_level_id" class="form-select"><option value="">كل الصفوف</option>@foreach($filterGradeLevels as $filterGradeLevel)<option value="{{ $filterGradeLevel->id }}" {{ $gradeLevelId == $filterGradeLevel->id ? 'selected' : '' }}>{{ $filterGradeLevel->name }} ({{ ($filterGradeLevel->track->value ?? $filterGradeLevel->track) === 'arabic' ? 'عربي' : 'لغات' }})</option>@endforeach</select></div>
                <div class="form-group"><label class="form-label" for="subject_id">المادة</label><select id="subject_id" name="subject_id" class="form-select"><option value="">كل المواد</option>@foreach($filterSubjects as $filterSubject)<option value="{{ $filterSubject->id }}" {{ $subjectId == $filterSubject->id ? 'selected' : '' }}>{{ $filterSubject->name_ar ?? $filterSubject->name }}</option>@endforeach</select></div>
                <div class="form-group"><label class="form-label" for="student_id">الطالب</label><select id="student_id" name="student_id" class="form-select"><option value="">كل الطلاب</option>@foreach($filterStudents as $filterStudent)<option value="{{ $filterStudent->id }}" {{ $studentId == $filterStudent->id ? 'selected' : '' }}>{{ $filterStudent->full_name }}</option>@endforeach</select></div>
                <div class="form-group"><label class="form-label" for="attendance">الحضور المسجل</label><select id="attendance" name="attendance" class="form-select"><option value="">كل الحالات</option><option value="present" {{ $attendance === 'present' ? 'selected' : '' }}>حاضر</option><option value="absent" {{ $attendance === 'absent' ? 'selected' : '' }}>غائب</option><option value="late" {{ $attendance === 'late' ? 'selected' : '' }}>متأخر</option></select></div>
                <div class="form-group"><label class="form-label" for="interaction">التفاعل المسجل</label><select id="interaction" name="interaction" class="form-select"><option value="">كل الحالات</option><option value="engaged" {{ $interaction === 'engaged' ? 'selected' : '' }}>متفاعل</option><option value="not_engaged" {{ $interaction === 'not_engaged' ? 'selected' : '' }}>غير متفاعل</option></select></div>
                <div class="form-group"><label class="form-label" for="status">حالة الإنجاز</label><select id="status" name="status" class="form-select"><option value="all" {{ $status === 'all' ? 'selected' : '' }}>كل الحالات</option><option value="complete" {{ $status === 'complete' ? 'selected' : '' }}>مكتمل</option><option value="incomplete" {{ $status === 'incomplete' ? 'selected' : '' }}>غير مكتمل</option><option value="started" {{ $status === 'started' ? 'selected' : '' }}>بدأ التسجيل</option><option value="not_started" {{ $status === 'not_started' ? 'selected' : '' }}>لم يبدأ</option></select></div>
            </div>
            <div style="display:flex;gap:.65rem;justify-content:flex-start;margin-top:.15rem;"><a href="{{ route('admin.teacher-evaluation-status.index') }}" class="btn-secondary"><i class="fas fa-rotate-right"></i> إعادة تعيين</a><button type="submit" class="btn-primary"><i class="fas fa-filter"></i> تطبيق الفلاتر</button></div>
        </form>
    </div>
</div>

<div class="teacher-status-summary">
    <div class="stat-card"><div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div><div><strong style="color:#0C7261;font-size:1.45rem;">{{ $overview['teachers'] }}</strong><div style="font-size:.78rem;color:#64748b;">مدرس مطابق للفلاتر</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:rgba(12,114,97,.12);color:#0C7261;"><i class="fas fa-circle-check"></i></div><div><strong style="color:#0C7261;font-size:1.45rem;">{{ $overview['complete'] }}</strong><div style="font-size:.78rem;color:#64748b;">أكملوا التسجيل</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:rgba(245,158,11,.14);color:#a16207;"><i class="fas fa-clock"></i></div><div><strong style="color:#a16207;font-size:1.45rem;">{{ $overview['incomplete'] }}</strong><div style="font-size:.78rem;color:#64748b;">لم يكتملوا بعد</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:rgba(239,68,68,.1);color:#dc2626;"><i class="fas fa-user-clock"></i></div><div><strong style="color:#dc2626;font-size:1.45rem;">{{ $overview['remaining_students'] }}</strong><div style="font-size:.78rem;color:#64748b;">إجمالي الطلاب المتبقين</div></div></div>
</div>

<div class="grid-2">
    @forelse($teacherStatuses as $item)
        @php
            $teacher = $item['teacher'];
            $percentage = $item['assigned_count'] ? round(($item['logged_count'] / $item['assigned_count']) * 100) : 0;
        @endphp
        <article class="teacher-status-card {{ $item['complete'] ? 'is-complete' : 'is-incomplete' }}">
            <div class="teacher-status-card-head">
                <div>
                    <div style="font-weight:800;color:#0C7261;font-size:1.05rem;"><i class="fas fa-chalkboard-teacher"></i> {{ $teacher->full_name }}</div>
                    <div style="font-size:.75rem;color:#64748b;margin-top:.35rem;">الصفوف: {{ $item['grade_levels']->pluck('name')->join('، ') ?: '—' }}</div>
                    <div style="font-size:.75rem;color:#64748b;margin-top:.2rem;">المواد: {{ $item['subjects']->map(fn ($subject) => $subject->name_ar ?? $subject->name)->join('، ') ?: '—' }}</div>
                </div>
                @if($item['complete'])
                    <span class="badge badge-green"><i class="fas fa-check-circle"></i> مكتمل</span>
                @elseif($item['logged_count'] > 0)
                    <span class="badge badge-yellow"><i class="fas fa-spinner"></i> قيد الإنجاز</span>
                @else
                    <span class="badge badge-red"><i class="fas fa-hourglass-start"></i> لم يبدأ</span>
                @endif
            </div>
            <div class="teacher-status-metrics"><div class="teacher-status-metric"><strong>{{ $item['assigned_count'] }}</strong><span>الطلاب المسندون</span></div><div class="teacher-status-metric"><strong>{{ $item['logged_count'] }}</strong><span>تم التسجيل لهم</span></div><div class="teacher-status-metric"><strong style="color:{{ $item['remaining_count'] ? '#dc2626' : '#0C7261' }};">{{ $item['remaining_count'] }}</strong><span>متبقٍ</span></div></div>
            <div style="height:9px;background:#e2e8f0;border-radius:99px;margin:0 1.25rem 1.15rem;overflow:hidden;"><div style="height:100%;width:{{ $percentage }}%;background:{{ $item['complete'] ? '#0C7261' : '#f59e0b' }};"></div></div>
            <div class="teacher-status-lists">
                <div class="teacher-status-list"><div class="teacher-status-list-title logged"><span><i class="fas fa-check"></i> تم التسجيل لهم</span><span>{{ $item['logged_count'] }}</span></div><div class="teacher-status-list-body">@forelse($item['logged_students'] as $student)<div class="teacher-status-student">{{ $student->full_name }} <span style="color:#94a3b8;">— {{ $student->gradeLevel?->name }}</span></div>@empty<div class="teacher-status-empty">لا توجد تقييمات مطابقة.</div>@endforelse</div></div>
                <div class="teacher-status-list"><div class="teacher-status-list-title remaining"><span><i class="fas fa-user-clock"></i> الطلاب المتبقون</span><span>{{ $item['remaining_count'] }}</span></div><div class="teacher-status-list-body">@forelse($item['remaining_students'] as $student)<div class="teacher-status-student">{{ $student->full_name }} <span style="color:#94a3b8;">— {{ $student->gradeLevel?->name }}</span></div>@empty<div class="teacher-status-empty" style="color:#0C7261;">اكتمل تسجيل جميع الطلاب.</div>@endforelse</div></div>
            </div>
            <div style="border-top:1px solid #edf1ef;padding:.75rem 1.25rem;text-align:left;"><a class="btn-secondary" style="font-size:.74rem;padding:.35rem .7rem;" href="{{ route('admin.progress.index', ['teacher_id' => $teacher->id, 'date_from' => $date->toDateString(), 'date_to' => $date->toDateString(), 'subject_id' => $subjectId]) }}"><i class="fas fa-list"></i> عرض السجلات</a></div>
        </article>
    @empty
        <div class="alert-error" style="grid-column:1/-1;justify-content:center;"><i class="fas fa-filter-circle-xmark"></i> لا توجد نتائج مطابقة للفلاتر المختارة.</div>
    @endforelse
</div>
@endsection
