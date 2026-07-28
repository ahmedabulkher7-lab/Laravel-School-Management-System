@extends('layouts.student')
@section('title', 'جدولي الدراسي')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">الجدول الدراسي الأسبوعي</h1>
        <div class="page-subtitle">جدول الحصص المخصص لـ {{ $student->gradeLevel->name }}</div>
    </div>
</div>

@php
    $days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
@endphp

@if($schedules->isEmpty())
    <div class="card"><div class="card-body" style="text-align:center;color:#475569;padding:3rem;">لم يتم رفع جدول لهذه المرحلة بعد.</div></div>
@else
    <div class="grid-2">
    @foreach([0,1,2,3,4] as $dayIndex) {{-- Sunday to Thursday --}}
        <div class="card">
            <div class="card-header" style="background:rgba(15,23,42,0.4);">
                <span class="card-title"><i class="fas fa-calendar-day" style="color:#f59e0b;margin-left:0.5rem;"></i> {{ $days[$dayIndex] }}</span>
            </div>
            <div class="card-body" style="padding:0;">
                @if(isset($schedules[$dayIndex]))
                    @foreach($schedules[$dayIndex] as $sched)
                        <div style="padding:1rem 1.5rem;border-bottom:1px solid rgba(12, 114, 97, 0.2);display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-weight:700;color:#0C7261;font-size:1.05rem;">
                                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $sched->subject->color ?? '#f59e0b' }};margin-left:0.4rem;"></span>
                                    {{ $sched->subject->name_ar ?? $sched->subject->name }}
                                </div>
                                <div style="color:#475569;font-size:0.85rem;margin-top:0.3rem;">
                                    <i class="fas fa-chalkboard-teacher"></i> {{ $sched->teacher->full_name }}
                                </div>
                            </div>
                            <div style="background:rgba(245,158,11,0.1);color:#fcd34d;padding:0.4rem 0.8rem;border-radius:0.5rem;font-size:0.85rem;font-weight:600;direction:ltr;">
                                {{ Carbon\Carbon::parse($sched->start_time)->format('h:i A') }} - {{ Carbon\Carbon::parse($sched->end_time)->format('h:i A') }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="padding:1.5rem;text-align:center;color:#475569;font-size:0.9rem;">يوم حر / لا يوجد حصص</div>
                @endif
            </div>
        </div>
    @endforeach
    </div>
@endif
@endsection
