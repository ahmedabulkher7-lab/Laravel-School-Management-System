@extends('layouts.teacher')
@section('title', 'تسجيل التقدم اليومي')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">تسجيل التقدم اليومي</h1>
        <div class="page-subtitle">إدخال سجل الحضور والتفاعل والدرجات لطلابك اليوم</div>
    </div>
</div>

@if($students->isEmpty())
    <div class="alert-error" style="justify-content:center;">
        لم يتم تعيين أي طلاب لك بعد، لا يمكنك إدخال سجلات التقدم.
    </div>
@else
    <div class="grid-2">
        @foreach($students as $student)
            @livewire('teacher.daily-progress-log', ['studentId' => $student->id], key($student->id))
        @endforeach
    </div>
@endif
@endsection
