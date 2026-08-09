@extends('layouts.teacher')
@section('title', 'الرئيسية')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">مرحباً أستاذ {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
        <div class="page-subtitle">نظرة عامة على مهامك اليومية</div>
    </div>
    <div style="font-size:0.85rem;color:#475569;">
        <i class="fas fa-calendar-alt"></i>
        {{ now()->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}
    </div>
</div>

<div class="grid-3" style="margin-bottom:2rem;">
    <div class="stat-card" style="background:rgba(16,185,129,0.08);border-color:rgba(16,185,129,0.25);">
        <div class="stat-icon" style="background:rgba(16,185,129,0.15);">
            <i class="fas fa-users" style="color:#059669;"></i>
        </div>
        <div>
            <div style="font-size:2rem;font-weight:800;color:#0C7261;">{{ $students->count() }}</div>
            <div style="font-size:0.82rem;color:#475569;">طالب مسجل لديك</div>
        </div>
    </div>

    <div class="stat-card" style="background:rgba(59,130,246,0.08);border-color:rgba(59,130,246,0.25);">
        <div class="stat-icon" style="background:rgba(59,130,246,0.15);">
            <i class="fas fa-check-double" style="color:#2563eb;"></i>
        </div>
        <div>
            <div style="font-size:2rem;font-weight:800;color:#0C7261;">{{ count($loggedToday) }}</div>
            <div style="font-size:0.82rem;color:#475569;">سجل تقدم تم إدخاله اليوم</div>
        </div>
    </div>

    <div class="stat-card" style="background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.25);">
        <div class="stat-icon" style="background:rgba(239,68,68,0.15);">
            <i class="fas fa-exclamation-circle" style="color:#dc2626;"></i>
        </div>
        <div>
            <div style="font-size:2rem;font-weight:800;color:#0C7261;">{{ $pendingStudents->count() }}</div>
            <div style="font-size:0.82rem;color:#475569;">طالب بانتظار إدخال السجل</div>
        </div>
    </div>
</div>

@if($weeklyPlanReminder)
<div class="card" style="border-color:rgba(245,158,11,0.45);margin-bottom:1.5rem;">
    <div class="card-header" style="background:rgba(245,158,11,0.08);">
        <span class="card-title" style="color:#92400e;">
            <i class="fas fa-calendar-week"></i> تذكير الجدول الأسبوعي
        </span>
        <a href="{{ $weeklyPlanReminder->data['url'] ?? route('teacher.weekly-plans.index') }}" class="btn-primary" style="padding:0.4rem 0.9rem;font-size:0.8rem;background:#d97706;">
            إدخال الجدول <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    <div class="card-body" style="color:#78350f;">
        {{ $weeklyPlanReminder->data['message'] ?? 'يرجى إدخال الجدول الأسبوعي للطلاب.' }}
    </div>
</div>
@endif

@if($pendingStudents->count() > 0)
<div class="card" style="border-color:rgba(239,68,68,0.3);">
    <div class="card-header" style="background:rgba(239,68,68,0.05);">
        <span class="card-title" style="color:#991b1b;">
            <i class="fas fa-bell"></i> تذكير: طلاب بانتظار إدخال التقدم اليومي
        </span>
        <a href="{{ route('teacher.progress.log') }}" class="btn-primary"
           style="padding:0.4rem 0.9rem;font-size:0.8rem;background:linear-gradient(135deg, #ef4444, #dc2626);color:white;">
            ابدأ تسجيل التقدم <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    <div class="card-body">
        {{-- Group by grade level --}}
        @foreach($pendingByGradeLevel as $gradeName => $groupStudents)
            <div style="margin-bottom:1rem;">
                <div style="font-size:0.8rem;font-weight:700;color:#475569;margin-bottom:0.5rem;
                            display:flex;align-items:center;gap:0.4rem;">
                    <i class="fas fa-chalkboard" style="color:#0C7261;"></i>
                    {{ $gradeName }}
                    <span style="background:#e2e8f0;color:#475569;border-radius:2rem;padding:0.1rem 0.5rem;font-size:0.72rem;">
                        {{ $groupStudents->count() }} طالب
                    </span>
                </div>
                <div style="display:flex;gap:0.6rem;flex-wrap:wrap;">
                    @foreach($groupStudents as $ps)
                        <span style="background:#ffffff;padding:0.4rem 0.85rem;border-radius:2rem;
                                     font-size:0.83rem;color:#0C7261;border:1px solid rgba(12,114,97,0.3);">
                            {{ $ps->full_name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@else
<div class="alert-success" style="justify-content:center;font-size:1.1rem;padding:2rem;">
    <i class="fas fa-star fa-2x" style="color:#facc15;margin-bottom:1rem;display:block;text-align:center;"></i>
    <div>عمل رائع! لقد قمت بإدخال سجلات التقدم لجميع طلابك اليوم.</div>
</div>
@endif
@endsection
