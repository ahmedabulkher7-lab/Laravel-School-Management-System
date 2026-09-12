@extends('layouts.teacher')
@section('title', 'حصصي اليوم')

@section('content')
<div class="page-header">
    <div><h1 class="page-title">حصصي اليوم</h1><div class="page-subtitle">التقييم مطلوب فقط للحصص المجدولة أدناه.</div></div>
    <form method="GET"><input type="date" name="date" value="{{ $date->toDateString() }}" class="form-input" onchange="this.form.submit()"></form>
</div>

@forelse($lessons as $lesson)
    @php($schedule = $lesson['schedule'])
    <div class="card" style="margin-bottom:1rem;padding:1.25rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
        <div>
            <div style="font-weight:800;color:#0C7261;font-size:1.05rem;">{{ $schedule->subject->name_ar ?? $schedule->subject->name }} — {{ $schedule->gradeLevel->name }}</div>
            <div style="margin-top:.4rem;color:#475569;direction:ltr;text-align:right;">{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</div>
        </div>
        <div style="display:flex;align-items:center;gap:.75rem;">
            <span class="badge {{ $lesson['complete'] ? 'badge-green' : 'badge-yellow' }}">{{ $lesson['completed'] }}/{{ $lesson['total'] }} طالب</span>
            <a class="btn-primary" href="{{ route('teacher.lessons.show', ['schedule' => $schedule, 'date' => $date->toDateString()]) }}">{{ $lesson['complete'] ? 'مراجعة التقييم' : 'تسجيل التقييم' }}</a>
        </div>
    </div>
@empty
    <div class="card" style="text-align:center;padding:2rem;color:#64748b;">لا توجد حصص مجدولة لك في هذا التاريخ.</div>
@endforelse
@endsection
