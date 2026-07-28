@extends('layouts.admin')
@section('title', 'لوحة التحكم')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">لوحة التحكم</h1>
        <div class="page-subtitle">مرحباً، {{ auth()->user()->name }} 👋</div>
    </div>
    <div style="font-size:0.85rem;color:#475569;">
        <i class="fas fa-calendar-alt"></i>
        {{ now()->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}
    </div>
</div>

<!-- Stats -->
<div class="grid-4" style="margin-bottom:2rem;">
    <div class="stat-card" style="background:rgba(99,102,241,0.08);border-color:rgba(99,102,241,0.25);">
        <div class="stat-icon" style="background:rgba(99,102,241,0.15);">
            <i class="fas fa-user-graduate" style="color:#0C7261;"></i>
        </div>
        <div>
            <div style="font-size:2rem;font-weight:800;color:#0C7261;">{{ $stats['students'] }}</div>
            <div style="font-size:0.82rem;color:#475569;">إجمالي الطلاب</div>
        </div>
    </div>

    <div class="stat-card" style="background:rgba(16,185,129,0.08);border-color:rgba(16,185,129,0.25);">
        <div class="stat-icon" style="background:rgba(16,185,129,0.15);">
            <i class="fas fa-chalkboard-teacher" style="color:#059669;"></i>
        </div>
        <div>
            <div style="font-size:2rem;font-weight:800;color:#0C7261;">{{ $stats['teachers'] }}</div>
            <div style="font-size:0.82rem;color:#475569;">المعلمون</div>
        </div>
    </div>

    <div class="stat-card" style="background:rgba(245,158,11,0.08);border-color:rgba(245,158,11,0.25);">
        <div class="stat-icon" style="background:rgba(245,158,11,0.15);">
            <i class="fas fa-file-pdf" style="color:#d97706;"></i>
        </div>
        <div>
            <div style="font-size:2rem;font-weight:800;color:#0C7261;">{{ $stats['reports'] }}</div>
            <div style="font-size:0.82rem;color:#475569;">التقارير المولّدة</div>
        </div>
    </div>

    <div class="stat-card" style="background:rgba(59,130,246,0.08);border-color:rgba(59,130,246,0.25);">
        <div class="stat-icon" style="background:rgba(59,130,246,0.15);">
            <i class="fas fa-chart-line" style="color:#2563eb;"></i>
        </div>
        <div>
            <div style="font-size:2rem;font-weight:800;color:#0C7261;">{{ $stats['attendance'] }}</div>
            <div style="font-size:0.82rem;color:#475569;">معدل الحضور</div>
        </div>
    </div>
</div>

<!-- Recent Progress -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-history" style="color:#0C7261;margin-left:0.5rem;"></i>آخر سجلات التقدم</span>
        <a href="{{ route('admin.progress.index') }}" class="btn-secondary" style="font-size:0.8rem;padding:0.4rem 0.9rem;">
            عرض الكل <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>الطالب</th>
                    <th>المادة</th>
                    <th>المعلم</th>
                    <th>التاريخ</th>
                    <th>الحضور</th>
                    <th>التفاعل</th>
                    <th>الواجب</th>
                    <th>الدرجة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentProgress as $rec)
                <tr>
                    <td style="font-weight:700;color:#0C7261;">{{ $rec->student?->full_name }}</td>
                    <td>{{ $rec->subject?->name_ar ?? $rec->subject?->name }}</td>
                    <td style="color:#475569;">{{ $rec->teacher?->full_name }}</td>
                    <td style="color:#475569;">{{ $rec->date->format('Y-m-d') }}</td>
                    <td>
                        @if($rec->attendance_status === 'present')
                            <span class="badge badge-green">حاضر</span>
                        @elseif($rec->attendance_status === 'absent')
                            <span class="badge badge-red">غائب</span>
                        @else
                            <span class="badge badge-yellow">متأخر</span>
                        @endif
                    </td>
                    <td>
                        @if($rec->interaction_level === 'engaged')
                            <span class="badge badge-purple">متفاعل</span>
                        @else
                            <span class="badge badge-gray">غير متفاعل</span>
                        @endif
                    </td>
                    <td>
                        @if($rec->homework_submitted)
                            <span class="badge badge-green">✓</span>
                        @else
                            <span class="badge badge-red">✗</span>
                        @endif
                    </td>
                    <td>
                        @if($rec->score !== null)
                            <span style="font-weight:700;color:{{ $rec->score >= 7 ? '#166534' : ($rec->score >= 5 ? '#ca8a04' : '#dc2626') }};">
                                {{ $rec->score }}/10
                            </span>
                        @else
                            <span style="color:#475569;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:#475569;padding:2rem;">
                        <i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:0.5rem;color:#475569;"></i>
                        لا توجد سجلات حتى الآن
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
