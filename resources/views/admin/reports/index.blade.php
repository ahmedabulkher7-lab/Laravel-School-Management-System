@extends('layouts.admin')
@section('title', 'التقارير الأسبوعية')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">توليد التقارير الأسبوعية</h1>
        <div class="page-subtitle">توليد تقرير شامل بتنسيق PDF لكل طالب بناءً على سجلات التقدم الأسبوعية</div>
    </div>
</div>

<div class="grid-2" style="align-items:start;">
    <!-- Generate Form -->
    <div class="card">
        <div class="card-header"><span class="card-title">توليد تقرير جديد</span></div>
        <div class="card-body">
            <form id="generate-form" action="" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">الطالب <span style="color:#ef4444">*</span></label>
                    <select id="student_select" class="form-select" required onchange="updateFormAction()">
                        <option value="">-- اختر الطالب --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->full_name }} ({{ $student->gradeLevel->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ بداية الأسبوع (عادة الأحد) <span style="color:#ef4444">*</span></label>
                    <input type="date" name="week_start" class="form-input" required>
                    <span class="form-error" style="color:#475569;font-size:0.75rem;">سيتم حساب 7 أيام تلقائياً من هذا التاريخ</span>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-top:1rem;">
                    <i class="fas fa-file-pdf"></i> توليد التقرير وإرسال إشعار
                </button>
            </form>
            <script>
                function updateFormAction() {
                    const select = document.getElementById('student_select');
                    const studentId = select.value;
                    const form = document.getElementById('generate-form');
                    if(studentId) {
                        form.action = `/admin/reports/generate/${studentId}`;
                    }
                }
            </script>
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
