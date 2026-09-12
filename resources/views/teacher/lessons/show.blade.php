@extends('layouts.teacher')
@section('title', 'تقييم الحصة')

@section('content')
<a href="{{ route('teacher.lessons.index', ['date' => $date->toDateString()]) }}" class="btn-secondary" style="margin-bottom:1rem;display:inline-flex;">العودة إلى حصصي</a>
<div class="page-header"><div><h1 class="page-title">{{ $schedule->subject->name_ar ?? $schedule->subject->name }} — {{ $schedule->gradeLevel->name }}</h1><div class="page-subtitle">{{ $date->locale('ar')->isoFormat('dddd، D MMMM') }} · {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }} · {{ $lesson['completed'] }}/{{ $lesson['total'] }} مكتمل</div></div></div>
<div class="grid-2">
@foreach($students as $student)
    @livewire('teacher.daily-progress-log', ['studentId' => $student->id, 'teacherId' => $schedule->teacher_id, 'subjectId' => $schedule->subject_id, 'scheduleId' => $schedule->id, 'lessonDate' => $date->toDateString()], key('schedule-'.$schedule->id.'-'.$date->toDateString().'-'.$student->id))
@endforeach
</div>
@endsection
