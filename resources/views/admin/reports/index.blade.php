@extends('layouts.admin')
@section('title', 'التقارير الأسبوعية')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">التقارير الأسبوعية</h1>
        <div class="page-subtitle">يمكن تحميل التقرير في أي وقت؛ وتظهر التقييمات غير المسجلة بوضوح داخل التقرير.</div>
    </div>

    <div style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
        {{-- زرار توليد جماعي --}}
        <button id="btn-generate-all" class="btn-primary" onclick="startGenerateAll()">
            <i class="fas fa-cogs"></i> توليد كل التقارير
        </button>

        {{-- زرار تحميل ZIP --}}
        <a href="{{ route('admin.reports.download-all', ['week_start' => $weekStart->toDateString()]) }}" class="btn-success" id="btn-download-all">
            <i class="fas fa-file-zipper"></i> تحميل الكل ZIP
        </a>

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
</div>

{{-- Progress Bar --}}
<div id="gen-progress-wrap" style="display:none;margin-bottom:1rem;background:#fff;border:1px solid #0C7261;border-radius:8px;padding:1rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
        <span style="font-weight:700;color:#0C7261;"><i class="fas fa-spinner fa-spin"></i> جاري توليد التقارير...</span>
        <span id="gen-count" style="font-size:0.85rem;color:#475569;">0 / 0</span>
    </div>
    <div style="background:#e2e8f0;border-radius:999px;height:10px;overflow:hidden;">
        <div id="gen-bar" style="height:100%;width:0%;background:#0C7261;border-radius:999px;transition:width 0.4s;"></div>
    </div>
    <div id="gen-status-text" style="font-size:0.78rem;color:#64748b;margin-top:0.4rem;"></div>
</div>

{{-- Alert اكتمال التوليد --}}
<div id="gen-done-alert" style="display:none;" class="alert-success" style="margin-bottom:1rem;">
    <i class="fas fa-check-circle"></i>
    <span id="gen-done-text"></span>
    — يمكنك الآن <a href="{{ route('admin.reports.download-all', ['week_start' => $weekStart->toDateString()]) }}" style="font-weight:bold;text-decoration:underline;">تحميل الكل ZIP</a>
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
                يمكنك تنزيل أي تقرير من الجدول أدناه حتى لو كانت بعض التقييمات ناقصة؛ وسيُنشأ التقرير بأحدث البيانات المتاحة.
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
                <thead><tr><th>الطالب</th><th>الصف</th><th>التقييمات المكتملة</th><th>الحالة</th><th style="text-align:center;">تحميل</th><th>التفاصيل</th></tr></thead>
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
                                <span class="badge badge-yellow"><i class="fas fa-clock"></i> غير مكتمل</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <a href="{{ route('admin.reports.student-download', ['student' => $student, 'week_start' => $weekStart->toDateString()]) }}" class="btn-success" style="padding:0.35rem 0.7rem;font-size:0.75rem;">
                                <i class="fas fa-download"></i> تحميل PDF
                            </a>
                        </td>
                        <td style="font-size:0.78rem;color:#a16207;">{{ implode('، ', $student->report_readiness['missing']) ?: 'كل التقييمات مكتملة' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:1.5rem;">لا يوجد طلاب مطابقون للفلتر.</td></tr>
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

@push('scripts')
<script>
const WEEK_START   = '{{ $weekStart->toDateString() }}';
const STATUS_URL   = '{{ route('admin.reports.generate-status') }}';
const GENERATE_URL = '{{ route('admin.reports.generate-all') }}';
const CSRF         = '{{ csrf_token() }}';

let pollInterval = null;

function startGenerateAll(force = false) {
    const btn = document.getElementById('btn-generate-all');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';

    fetch(GENERATE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ week_start: WEEK_START, force: force }),
    })
    .then(r => r.json())
    .then(() => {
        document.getElementById('gen-progress-wrap').style.display = 'block';
        document.getElementById('gen-done-alert').style.display    = 'none';
        startPolling();
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-cogs"></i> توليد كل التقارير';
        alert('حدث خطأ أثناء بدء التوليد.');
    });
}

function startPolling() {
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(checkStatus, 2000);
    checkStatus();
}

function checkStatus() {
    fetch(`${STATUS_URL}?week_start=${WEEK_START}`)
        .then(r => r.json())
        .then(data => {
            const wrap  = document.getElementById('gen-progress-wrap');
            const bar   = document.getElementById('gen-bar');
            const count = document.getElementById('gen-count');
            const text  = document.getElementById('gen-status-text');
            const btn   = document.getElementById('btn-generate-all');

            if (data.status === 'idle') {
                clearInterval(pollInterval);
                wrap.style.display = 'none';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-cogs"></i> توليد كل التقارير';
                return;
            }

            if (data.status === 'done') {
                clearInterval(pollInterval);
                wrap.style.display = 'none';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-cogs"></i> إعادة التوليد';

                const doneAlert = document.getElementById('gen-done-alert');
                document.getElementById('gen-done-text').textContent =
                    `تم توليد ${data.done} تقرير بنجاح` + (data.failed > 0 ? ` (فشل ${data.failed})` : '');
                doneAlert.style.display = 'block';
                return;
            }

            // running أو starting
            wrap.style.display = 'block';
            const pct = data.total > 0 ? Math.round((data.done / data.total) * 100) : 0;
            bar.style.width   = pct + '%';
            count.textContent = `${data.done} / ${data.total}`;
            text.textContent  = `${pct}% مكتمل${data.failed > 0 ? ` — فشل: ${data.failed}` : ''}`;
            btn.innerHTML     = '<i class="fas fa-spinner fa-spin"></i> جاري التوليد...';
        })
        .catch(() => {});
}

// تحقق تلقائي عند تحميل الصفحة لو فيه عملية شغّالة
document.addEventListener('DOMContentLoaded', () => checkStatus());
</script>
@endpush
