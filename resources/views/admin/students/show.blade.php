@extends('layouts.admin')
@section('title', 'تفاصيل الطالب')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">ملف الطالب: {{ $student->full_name }}</h1>
        <div class="page-subtitle">استعراض كافة بيانات وسجلات الطالب</div>
    </div>
    <div style="display:flex;gap:0.5rem;">
        <a href="{{ route('admin.students.edit', $student) }}" class="btn-secondary" style="color:#facc15;">
            <i class="fas fa-edit"></i> تعديل البيانات
        </a>
        <a href="{{ route('admin.students.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-right"></i> عودة
        </a>
    </div>
</div>

<div class="grid-2">
    <!-- Student Info -->
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><span class="card-title">المعلومات الشخصية والدراسية</span></div>
        <div class="card-body">
            <table style="width:100%;line-height:2.5;font-size:0.9rem;">
                <tr>
                    <td style="color:#475569;width:40%;">الاسم الكامل:</td>
                    <td style="color:#0C7261;font-weight:600;">{{ $student->full_name }}</td>
                </tr>
                <tr>
                    <td style="color:#475569;">المرحلة الدراسية:</td>
                    <td><span class="badge badge-blue">{{ $student->gradeLevel->name }}</span></td>
                </tr>
                <tr>
                    <td style="color:#475569;">البريد الإلكتروني:</td>
                    <td style="color:#0C7261;">{{ $student->user->email }}</td>
                </tr>
                <tr>
                    <td style="color:#475569;">تاريخ الميلاد:</td>
                    <td style="color:#0C7261;">{{ $student->date_of_birth->format('Y-m-d') }} ({{ $student->age }} سنة)</td>
                </tr>
                <tr>
                    <td style="color:#475569;">تاريخ الالتحاق:</td>
                    <td style="color:#0C7261;">{{ $student->enrollment_date->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <td style="color:#475569;">اسم ولي الأمر:</td>
                    <td style="color:#0C7261;">{{ $student->guardian_name }}</td>
                </tr>
                <tr>
                    <td style="color:#475569;">هاتف ولي الأمر:</td>
                    <td style="color:#0C7261;">{{ $student->guardian_phone }}</td>
                </tr>
                @if($student->phone)
                <tr>
                    <td style="color:#475569;">هاتف الطالب:</td>
                    <td style="color:#0C7261;">{{ $student->phone }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Assigned Teachers -->
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><span class="card-title">المعلمون المعينون</span></div>
        <div class="card-body" style="padding:0;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>المادة</th>
                        <th>المعلم</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($student->assignments as $assignment)
                    <tr>
                        <td>{{ $assignment->subject->name_ar ?? $assignment->subject->name }}</td>
                        <td>{{ $assignment->teacher->full_name }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" style="text-align:center;padding:1.5rem;">لم يتم تعيين معلمين بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><span class="card-title">أحدث التقارير الأسبوعية</span></div>
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>تاريخ الأسبوع</th>
                    <th>تاريخ التوليد</th>
                    <th style="text-align:center;">تحميل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($student->weeklyReports->take(5) as $report)
                <tr>
                    <td style="color:#0C7261;">{{ $report->week_start_date }} إلى {{ $report->week_end_date }}</td>
                    <td style="color:#475569;">{{ $report->generated_at->format('Y-m-d H:i') }}</td>
                    <td style="text-align:center;">
                        <a href="{{ route('admin.reports.download', $report) }}" class="btn-success" style="padding:0.3rem 0.6rem;font-size:0.75rem;">
                            <i class="fas fa-download"></i> PDF
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align:center;padding:1.5rem;">لم يتم توليد تقارير أسبوعية بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
