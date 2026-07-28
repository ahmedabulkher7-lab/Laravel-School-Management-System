@extends('layouts.student')
@section('title', 'تقاريري')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">التقارير الأسبوعية</h1>
        <div class="page-subtitle">تقييمات الأداء الأسبوعية المجمعة من الإدارة</div>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>تاريخ التقرير (أسبوعي)</th>
                    <th>تاريخ الإصدار</th>
                    <th style="text-align:center;">تحميل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                <tr>
                    <td style="font-weight:600;color:#0C7261;direction:ltr;text-align:right;">
                        <span style="color:#f59e0b;"><i class="fas fa-calendar-week"></i></span>
                        {{ $report->week_start_date }} - {{ $report->week_end_date }}
                    </td>
                    <td style="color:#475569;direction:ltr;text-align:right;">{{ $report->generated_at->format('Y-m-d H:i') }}</td>
                    <td style="text-align:center;">
                        <a href="{{ route('student.reports.download', $report) }}" class="btn-success" style="padding:0.4rem 0.8rem;font-size:0.8rem;">
                            <i class="fas fa-download"></i> PDF
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align:center;padding:2rem;">لم يتم إصدار تقارير لك حتى الآن.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reports->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid rgba(12, 114, 97, 0.2);">
        {{ $reports->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
