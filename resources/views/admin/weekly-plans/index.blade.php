@extends('layouts.admin')
@section('title', 'الجداول الأسبوعية')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">الجداول الأسبوعية</h1>
        <div class="page-subtitle">مراجعة خطط الصفوف وتحميل جدول كل صف بعد اكتمال خطط المدرسين</div>
    </div>
    <form method="GET" action="{{ route('admin.weekly-plans.index') }}" style="display:flex;align-items:end;gap:0.6rem;">
        <div>
            <label class="form-label" style="font-size:0.78rem;">أسبوع يبدأ في</label>
            <input type="date" name="week_start" value="{{ $weekStart->toDateString() }}" class="form-input" style="min-width:170px;">
        </div>
        <div>
            <label class="form-label" style="font-size:0.78rem;">المسار</label>
            <select name="track" class="form-select" style="min-width:130px;">
                <option value="">كل المسارات</option>
                @foreach($tracks as $trackItem)
                    <option value="{{ $trackItem->value }}" {{ $track === $trackItem->value ? 'selected' : '' }}>
                        {{ $trackItem->value === 'arabic' ? 'عربي' : 'لغات' }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="btn-primary" type="submit"><i class="fas fa-filter"></i> عرض</button>
    </form>
</div>

<div class="alert-success" style="margin-bottom:1.25rem;">
    <i class="fas fa-calendar-week"></i>
    خطة الأسبوع: {{ $weekStart->locale('ar')->isoFormat('D MMMM') }} إلى {{ $weekStart->copy()->addDays(6)->locale('ar')->isoFormat('D MMMM YYYY') }}
</div>

<div class="grid-2">
    @forelse($summaries as $summary)
        @php $gradeLevel = $summary['gradeLevel'] ?? null; @endphp
        <div class="card" style="overflow:hidden;border:2px solid {{ $summary['complete'] ? '#ef4444' : 'rgba(12,114,97,0.16)' }};">
            <div class="card-header" style="background:{{ $summary['complete'] ? '#fef2f2' : 'rgba(12,114,97,0.06)' }};padding:1.1rem 1.25rem;">
                <div>
                    <div class="card-title" style="color:{{ $summary['complete'] ? '#b91c1c' : '#0C7261' }};font-size:1.08rem;font-weight:800;">
                        <i class="fas fa-chalkboard"></i> {{ $summary['name'] }}
                    </div>
                    <div style="font-size:0.88rem;color:#475569;margin-top:0.35rem;font-weight:600;">
                        مسار {{ $summary['track'] === 'arabic' ? 'عربي' : 'لغات' }}
                    </div>
                </div>
                @if($summary['complete'])
                    <a href="{{ route('admin.weekly-plans.download', $summary['id']) }}?week_start={{ $weekStart->toDateString() }}" class="btn-primary" style="padding:0.45rem 0.8rem;font-size:0.78rem;background:#dc2626;">
                        <i class="fas fa-file-pdf"></i> تحميل PDF
                    </a>
                @else
                    <span class="badge badge-yellow"><i class="fas fa-clock"></i> غير مكتمل</span>
                @endif
            </div>
            <div class="card-body">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                    <span style="font-size:0.95rem;color:#334155;font-weight:700;">خطط المدرسين</span>
                    <strong style="font-size:1.05rem;color:{{ $summary['complete'] ? '#dc2626' : '#0C7261' }};">
                        {{ $summary['completed'] }} / {{ $summary['required'] }}
                    </strong>
                </div>
                <div style="height:11px;background:#e2e8f0;border-radius:1rem;overflow:hidden;margin-bottom:1.1rem;">
                    <div style="height:100%;width:{{ $summary['required'] ? round(($summary['completed'] / $summary['required']) * 100) : 0 }}%;background:{{ $summary['complete'] ? '#ef4444' : '#0C7261' }};"></div>
                </div>
                <div style="max-height:210px;overflow:auto;">
                    @foreach($summary['rows'] as $row)
                        <div style="padding:0.8rem 0;border-bottom:1px solid #dbe3ea;display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;">
                            <span style="font-size:0.98rem;font-weight:800;color:#1e293b;">{{ $row['subject']->name_ar ?? $row['subject']->name }}</span>
                            @if($row['complete'])
                                <span style="color:#dc2626;font-size:0.88rem;font-weight:800;white-space:nowrap;"><i class="fas fa-check-circle"></i> مكتملة</span>
                            @else
                                <span style="color:#a16207;font-size:0.86rem;font-weight:600;text-align:left;">
                                    ناقص: {{ $row['teachers']->reject(fn ($teacher) => $row['plans']->contains('teacher_id', $teacher->id))->pluck('full_name')->join('، ') ?: 'لا يوجد مدرس مسند' }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <div class="alert-error" style="grid-column:1/-1;justify-content:center;">لا توجد صفوف مطابقة للفلاتر.</div>
    @endforelse
</div>
@endsection
