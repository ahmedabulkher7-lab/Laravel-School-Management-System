@extends('layouts.admin')
@section('title', 'الجداول الدراسية')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">الجداول الدراسية</h1>
        <div class="page-subtitle">إدارة الحصص وجداول المراحل الدراسية</div>
    </div>
    <a href="{{ route('admin.schedules.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> إضافة حصة جديدة
    </a>
</div>

@php
    $days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
@endphp

<!-- Filter -->
<div class="card" style="margin-bottom:1.5rem;background:#ffffff;">
    <div class="card-body" style="padding:1rem 1.5rem;">
        <form method="GET" action="{{ route('admin.schedules.index') }}" style="display:flex;gap:1rem;align-items:flex-end;">
            <div style="flex:1;">
                <label class="form-label">تصفية حسب المرحلة</label>
                <select name="grade_level_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- جميع المراحل --</option>
                    @foreach($gradeLevels as $gl)
                        <option value="{{ $gl->id }}" {{ request('grade_level_id') == $gl->id ? 'selected' : '' }}>{{ $gl->name }}</option>
                    @endforeach
                </select>
            </div>
            @if(request('grade_level_id'))
                <a href="{{ route('admin.schedules.index') }}" class="btn-secondary" style="height:42px;display:flex;align-items:center;">إلغاء</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>المرحلة الدراسية</th>
                    <th>اليوم</th>
                    <th>الوقت</th>
                    <th>المادة</th>
                    <th>المعلم</th>
                    <th style="text-align:center;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $filteredSchedules = request('grade_level_id') 
                        ? $schedules->where('grade_level_id', request('grade_level_id')) 
                        : $schedules;
                @endphp
                @forelse($filteredSchedules as $schedule)
                <tr>
                    <td><span class="badge badge-blue">{{ $schedule->gradeLevel->name }}</span></td>
                    <td style="font-weight:600;color:#0C7261;">{{ $days[$schedule->day_of_week] }}</td>
                    <td style="color:#0C7261;direction:ltr;text-align:right;">
                        {{ Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - {{ Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                    </td>
                    <td>{{ $schedule->subject->name_ar ?? $schedule->subject->name }}</td>
                    <td style="color:#475569;">{{ $schedule->teacher->full_name }}</td>
                    <td style="text-align:center;">
                        <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn-secondary" style="padding:0.4rem 0.6rem;font-size:0.8rem;color:#facc15;border-color:rgba(250,204,21,0.3);">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد؟');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-secondary" style="padding:0.4rem 0.6rem;font-size:0.8rem;color:#f87171;border-color:rgba(248,113,113,0.3);">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:2rem;">لا توجد حصص مجدولة.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
