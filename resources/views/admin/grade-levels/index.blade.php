@extends('layouts.admin')
@section('title', 'المراحل الدراسية')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">المراحل الدراسية</h1>
        <div class="page-subtitle">إدارة المراحل (صفوف المدرسة)</div>
    </div>
    <a href="{{ route('admin.grade-levels.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> إضافة مرحلة
    </a>
</div>

<div class="card" style="max-width:800px;">
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>الترتيب</th>
                    <th>اسم المرحلة</th>
                    <th>عدد الطلاب</th>
                    <th style="text-align:center;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gradeLevels as $gl)
                <tr>
                    <td style="color:#475569;font-weight:700;">{{ $gl->order }}</td>
                    <td style="font-weight:600;color:#0C7261;">{{ $gl->name }}</td>
                    <td><span class="badge badge-purple">{{ $gl->students_count }} طالب</span></td>
                    <td style="text-align:center;">
                        <a href="{{ route('admin.grade-levels.edit', $gl) }}" class="btn-secondary" style="padding:0.4rem 0.6rem;font-size:0.8rem;color:#facc15;border-color:rgba(250,204,21,0.3);">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.grade-levels.destroy', $gl) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد؟');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-secondary" style="padding:0.4rem 0.6rem;font-size:0.8rem;color:#f87171;border-color:rgba(248,113,113,0.3);">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;padding:2rem;">لا يوجد مراحل مسجلة.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
