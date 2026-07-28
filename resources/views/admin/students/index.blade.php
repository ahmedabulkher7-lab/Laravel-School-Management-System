@extends('layouts.admin')
@section('title', 'الطلاب')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إدارة الطلاب</h1>
        <div class="page-subtitle">قائمة بجميع الطلاب المسجلين في المدرسة</div>
    </div>
    <a href="{{ route('admin.students.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> إضافة طالب جديد
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>اسم الطالب</th>
                    <th>المرحلة الدراسية</th>
                    <th>البريد الإلكتروني</th>
                    <th>تاريخ التسجيل</th>
                    <th style="text-align:center;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td style="font-weight:600;color:#0C7261;">{{ $student->full_name }}</td>
                    <td><span class="badge badge-blue">{{ $student->gradeLevel->name }}</span></td>
                    <td style="color:#475569;">{{ $student->user->email }}</td>
                    <td style="color:#475569;">{{ $student->enrollment_date->format('Y-m-d') }}</td>
                    <td style="text-align:center;">
                        <a href="{{ route('admin.students.show', $student) }}" class="btn-secondary" style="padding:0.4rem 0.6rem;font-size:0.8rem;">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.students.edit', $student) }}" class="btn-secondary" style="padding:0.4rem 0.6rem;font-size:0.8rem;color:#facc15;border-color:rgba(250,204,21,0.3);">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب؟');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-secondary" style="padding:0.4rem 0.6rem;font-size:0.8rem;color:#f87171;border-color:rgba(248,113,113,0.3);">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:2rem;color:#475569;">لا يوجد طلاب مسجلين حتى الآن.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($students->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid rgba(12, 114, 97, 0.2);">
        {{ $students->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
