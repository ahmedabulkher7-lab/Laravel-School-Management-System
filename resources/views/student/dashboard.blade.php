@extends('layouts.student')
@section('title', 'الرئيسية')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">أهلاً بك، {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
        <div class="page-subtitle">نظرة عامة على نشاطك ومستوى تقدمك</div>
    </div>
</div>

<div class="grid-2">
    <!-- Student Details -->
    <div class="card" style="background:#ffffff;border-color:#0C7261;border-width:2px;">
        <div class="card-body" style="display:flex;gap:1.5rem;align-items:center;">
            <div style="width:80px;height:80px;border-radius:50%;background:rgba(12, 114, 97, 0.1);display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:#0C7261;">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <h2 style="font-size:1.4rem;font-weight:800;color:#0C7261;margin:0 0 0.5rem 0;">{{ $student->full_name }}</h2>
                <div style="color:#475569;font-size:0.95rem;font-weight:700;"><i class="fas fa-layer-group" style="color:#F8EB2F;margin-left:0.3rem;"></i> {{ $student->gradeLevel->name }}</div>
                <div style="color:#475569;font-size:0.85rem;margin-top:0.3rem;">تاريخ الالتحاق: {{ $student->enrollment_date->format('Y-m-d') }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid-2">
        <div class="stat-card" style="padding:1.2rem;">
            <div class="stat-icon" style="background:rgba(16,185,129,0.15);width:3rem;height:3rem;font-size:1.2rem;"><i class="fas fa-chart-line" style="color:#059669;"></i></div>
            <div>
                <div style="font-size:1.5rem;font-weight:800;color:#0C7261;">{{ $recentProgress->where('score', '>=', 7)->count() }}</div>
                <div style="font-size:0.75rem;color:#475569;font-weight:700;">درجات ممتازة مؤخراً</div>
            </div>
        </div>
        <div class="stat-card" style="padding:1.2rem;">
            <div class="stat-icon" style="background:rgba(59,130,246,0.15);width:3rem;height:3rem;font-size:1.2rem;"><i class="fas fa-book" style="color:#2563eb;"></i></div>
            <div>
                <div style="font-size:1.5rem;font-weight:800;color:#0C7261;">{{ $student->assignments()->count() }}</div>
                <div style="font-size:0.75rem;color:#475569;font-weight:700;">مواد تدرسها</div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-history" style="color:#0C7261;margin-left:0.5rem;"></i> آخر المتابعات اليومية من معلميك</span>
        <a href="{{ route('student.performance.index') }}" class="btn-secondary" style="font-size:0.8rem;padding:0.4rem 0.9rem;">
            التفاصيل <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>المادة</th>
                    <th>التاريخ</th>
                    <th>الحضور</th>
                    <th>الواجب</th>
                    <th>الدرجة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentProgress as $rec)
                <tr>
                    <td style="font-weight:700;color:#0C7261;">{{ $rec->subject->name_ar ?? $rec->subject->name }}</td>
                    <td style="color:#475569;direction:ltr;text-align:right;">{{ $rec->date->format('Y-m-d') }}</td>
                    <td>
                        @if($rec->attendance_status === 'present') <span style="color:#166534;"><i class="fas fa-check"></i></span>
                        @elseif($rec->attendance_status === 'absent') <span style="color:#dc2626;"><i class="fas fa-times"></i></span>
                        @else <span style="color:#ca8a04;"><i class="fas fa-clock"></i></span> @endif
                    </td>
                    <td>
                        @if($rec->homework_submitted) <span class="badge badge-green">سُلِّم</span>
                        @else <span class="badge badge-red">لم يُسلّم</span> @endif
                    </td>
                    <td>
                        @if($rec->score !== null)
                            <span style="font-weight:700;color:{{ $rec->score >= 7 ? '#166534' : ($rec->score >= 5 ? '#ca8a04' : '#dc2626') }};">
                                {{ $rec->score }}/10
                            </span>
                        @else <span style="color:#475569;">—</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:2rem;">لا توجد سجلات بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
