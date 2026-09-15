@extends('layouts.teacher')
@section('title', 'السجل التاريخي للتقدم')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">السجل التاريخي</h1>
        <div class="page-subtitle">مراجعة كافة سجلات التقدم التي قمت بإدخالها مسبقاً</div>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>اسم الطالب</th>
                    <th>الحضور</th>
                    <th>التفاعل</th>
                    <th>الدرجة</th>
                    <th>الملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $rec)
                <tr>
                    <td style="color:#475569;direction:ltr;text-align:right;">{{ $rec->date->format('Y-m-d') }}</td>
                    <td style="font-weight:600;color:#0C7261;">{{ $rec->student->full_name }}</td>
                    <td>
                        @php
                            $att = $rec->attendance_status instanceof \BackedEnum ? $rec->attendance_status->value : (string) $rec->attendance_status;
                        @endphp
                        @if($att === 'present') <span class="badge badge-green">حاضر</span>
                        @elseif($att === 'absent') <span class="badge badge-red">غائب</span>
                        @else <span class="badge badge-yellow">متأخر</span> @endif
                    </td>
                    <td>
                        @php
                            $act = $rec->interaction_level instanceof \BackedEnum ? $rec->interaction_level->value : (string) $rec->interaction_level;
                        @endphp
                        @if($act === 'engaged') <span class="badge badge-purple">متفاعل</span>
                        @else <span class="badge badge-gray">غير متفاعل</span> @endif
                    </td>
    
                    <td>
                        @if($rec->score !== null)
                            <span style="font-weight:700;color:{{ $rec->score >= 7 ? '#4ade80' : ($rec->score >= 5 ? '#facc15' : '#f87171') }};">
                                {{ $rec->score }}/10
                            </span>
                        @else <span style="color:#475569;">—</span> @endif
                    </td>
                    <td style="color:#475569;font-size:0.8rem;">{{ Str::limit($rec->comment, 30, '...') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:2rem;">لا توجد سجلات.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($history->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid rgba(12, 114, 97, 0.2);">
        {{ $history->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
