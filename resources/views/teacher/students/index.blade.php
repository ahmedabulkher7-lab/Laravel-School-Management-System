@extends('layouts.teacher')
@section('title', 'قائمة الطلاب')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">طلابي</h1>
        <div class="page-subtitle">قائمة بجميع الطلاب المعينين لك في مادة {{ $teacher->subjects->pluck('name_ar')->join(', ') }}</div>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>اسم الطالب</th>
                    <th>المرحلة الدراسية</th>
                    <th>تاريخ الميلاد</th>
                    <th>هاتف ولي الأمر</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td style="font-weight:600;color:#0C7261;">
                        <i class="fas fa-user-graduate" style="color:#34d399;margin-left:0.4rem;"></i>
                        {{ $student->full_name }}
                    </td>
                    <td><span class="badge badge-green">{{ $student->gradeLevel->name }}</span></td>
                    <td style="color:#475569;direction:ltr;text-align:right;">{{ $student->date_of_birth->format('Y-m-d') }}</td>
                    <td style="color:#0C7261;direction:ltr;text-align:right;">{{ $student->guardian_phone }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;padding:2rem;">لم يتم تعيين طلاب لك بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
