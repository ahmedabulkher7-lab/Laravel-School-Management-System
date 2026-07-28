@extends('layouts.admin')
@section('title', 'سجلات التقدم')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">متابعة سجلات التقدم</h1>
        <div class="page-subtitle">استعراض كافة الإدخالات اليومية للمعلمين</div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom:1.5rem;background:#ffffff;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.progress.index') }}">
            <div class="grid-4">
                <div class="form-group">
                    <label class="form-label">الطالب</label>
                    <select name="student_id" class="form-select">
                        <option value="">-- الكل --</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}" {{ request('student_id') == $st->id ? 'selected' : '' }}>{{ $st->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">المعلم</label>
                    <select name="teacher_id" class="form-select">
                        <option value="">-- الكل --</option>
                        @foreach($teachers as $tc)
                            <option value="{{ $tc->id }}" {{ request('teacher_id') == $tc->id ? 'selected' : '' }}>{{ $tc->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">التاريخ (من)</label>
                    <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">التاريخ (إلى)</label>
                    <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}">
                </div>
            </div>
            <div style="text-align:left;">
                <a href="{{ route('admin.progress.index') }}" class="btn-secondary">إلغاء التصفية</a>
                <button type="submit" class="btn-primary"><i class="fas fa-search"></i> بحث</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>الطالب</th>
                    <th>المادة</th>
                    <th>المعلم</th>
                    <th>التاريخ</th>
                    <th>الحضور</th>
                    <th>التفاعل</th>
                    <th>الدرجة</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($progressRecords as $rec)
                <tr>
                    <td style="font-weight:600;color:#0C7261;">{{ $rec->student?->full_name }}</td>
                    <td>{{ $rec->subject?->name_ar ?? $rec->subject?->name }}</td>
                    <td style="color:#475569;">{{ $rec->teacher?->full_name }}</td>
                    <td style="color:#475569;">{{ $rec->date->format('Y-m-d') }}</td>
                    <td>
                        @if($rec->attendance_status === 'present') <span class="badge badge-green">حاضر</span>
                        @elseif($rec->attendance_status === 'absent') <span class="badge badge-red">غائب</span>
                        @else <span class="badge badge-yellow">متأخر</span> @endif
                    </td>
                    <td>
                        @if($rec->interaction_level === 'engaged') <span class="badge badge-purple">متفاعل</span>
                        @else <span class="badge badge-gray">غير متفاعل</span> @endif
                    </td>
                    <td>
                        @if($rec->score !== null)
                            <span style="font-weight:700;color:{{ $rec->score >= 7 ? '#4ade80' : ($rec->score >= 5 ? '#facc15' : '#f87171') }};">
                                {{ $rec->score }}/10
                            </span>
                        @else <span style="color:#475569;">—</span> @endif
                    </td>
                    <td style="color:#475569;font-size:0.8rem;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $rec->comment }}">
                        {{ $rec->comment ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:2rem;">لا توجد سجلات تقدم تطابق البحث.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($progressRecords->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid rgba(12, 114, 97, 0.2);">
        {{ $progressRecords->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
