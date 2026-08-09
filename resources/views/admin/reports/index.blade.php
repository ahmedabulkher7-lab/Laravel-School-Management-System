@extends('layouts.admin')
@section('title', 'التقارير الأسبوعية')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">التقارير الأسبوعية</h1>
        <div class="page-subtitle">تُنشأ التقارير تلقائيًا بعد اكتمال تقييمات الأسبوع</div>
    </div>
        <form method="GET" action="{{ route('admin.reports.index') }}" style="display:flex;align-items:end;gap:0.6rem;">
            <div>
                <label class="form-label" style="font-size:0.78rem;">أسبوع يبدأ في</label>
                <input type="date" name="week_start" value="{{ $weekStart->toDateString() }}" class="form-input">
            </div>
            <div>
                <label class="form-label" style="font-size:0.78rem;">حالة التقرير</label>
                <select name="status" class="form-select">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>كل الطلاب</option>
                    <option value="ready" {{ $status === 'ready' ? 'selected' : '' }}>التقرير جاهز</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>غير جاهز</option>
                </select>
            </div>
            <button type="submit" class="btn-secondary"><i class="fas fa-filter"></i> فلترة</button>
        </form>
</div>

    <div class="alert-success" style="margin-bottom:1.25rem;">
        <i class="fas fa-calendar-week"></i>
        حالة التقييم للأسبوع: {{ $weekStart->locale('ar')->isoFormat('D MMMM YYYY') }} إلى {{ $weekStart->copy()->addDays(6)->locale('ar')->isoFormat('D MMMM YYYY') }}
    </div>

<div class="grid-2" style="align-items:start;">
    <!-- Automatic generation -->
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-magic"></i> التوليد التلقائي</span></div>
        <div class="card-body" style="line-height:1.8;color:#475569;">
            <i class="fas fa-info-circle" style="color:#0C7261;"></i>
            يتم إنشاء التقرير تلقائيًا وإرساله للطالب فور تسجيل تقييمات جميع المواد لأيام الأسبوع الدراسي.
            <div style="margin-top:0.75rem;font-size:0.82rem;color:#64748b;">
                لا يحتاج المدير إلى الضغط على زر التوليد. استخدم جدول الحالة أدناه لمعرفة الطلاب الجاهزين.
            </div>
        </div>
    </div>

    <!-- Readiness -->
    <div class="card" style="grid-column:1/-1;">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-clipboard-check"></i> حالة تقارير الطلاب</span>
            <span style="font-size:0.82rem;color:#475569;">{{ $readyCount }} جاهز / {{ $pendingCount }} غير جاهز</span>
        </div>
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table class="data-table">
                <thead><tr><th>الطالب</th><th>الصف</th><th>التقييمات المكتملة</th><th>الحالة</th><th>التفاصيل</th></tr></thead>
                <tbody>
                @forelse($students as $student)
                    <tr>
                        <td style="font-weight:700;color:#0C7261;">{{ $student->full_name }}</td>
                        <td>{{ $student->gradeLevel->name }}</td>
                        <td>{{ $student->report_readiness['completed'] }} / {{ $student->report_readiness['required'] }} مادة</td>
                        <td>
                            @if($student->report_readiness['ready'])
                                <span class="badge badge-green"><i class="fas fa-check-circle"></i> جاهز</span>
                            @else
                                <span class="badge badge-yellow"><i class="fas fa-clock"></i> غير جاهز</span>
                            @endif
                        </td>
                        <td style="font-size:0.78rem;color:#a16207;">{{ implode('، ', $student->report_readiness['missing']) ?: 'كل التقييمات مكتملة' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;padding:1.5rem;">لا يوجد طلاب مطابقون للفلتر.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- History -->
    <div class="card">
        <div class="card-header"><span class="card-title">آخر التقارير المولّدة</span></div>
        <div class="card-body" style="padding:0;max-height:500px;overflow-y:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الطالب</th>
                        <th>تاريخ الأسبوع</th>
                        <th style="text-align:center;">تحميل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td style="font-weight:600;color:#0C7261;">{{ $report->student->full_name }}</td>
                        <td style="color:#475569;font-size:0.8rem;direction:ltr;text-align:right;">
                            {{ $report->week_start_date }}<br>to {{ $report->week_end_date }}
                        </td>
                        <td style="text-align:center;">
                            <a href="{{ route('admin.reports.download', $report) }}" class="btn-success" style="padding:0.3rem 0.6rem;font-size:0.75rem;">
                                <i class="fas fa-download"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;padding:1.5rem;">لم يتم توليد تقارير.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
        <div style="padding:1rem;border-top:1px solid rgba(12, 114, 97, 0.2);">
            {{ $reports->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>
@endsection
