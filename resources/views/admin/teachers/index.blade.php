@extends('layouts.admin')
@section('title', 'المعلمون')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إدارة المعلمين</h1>
        <div class="page-subtitle">قائمة بجميع المعلمين في المدرسة</div>
    </div>
    <a href="{{ route('admin.teachers.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> إضافة معلم جديد
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>اسم المعلم</th>
                    <th>المادة الدراسية</th>
                    <th>المراحل التي يدرسها</th>
                    <th>البريد الإلكتروني</th>
                    <th style="text-align:center;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $teacher)
                <tr>
                    <td style="font-weight:600;color:#0C7261;">{{ $teacher->full_name }}</td>
                    <td>
                        @if($teacher->subjects->isNotEmpty())
                            <span class="badge" style="background:{{ $teacher->subjects->first()->color ?? '#ccc' }}22; color:{{ $teacher->subjects->first()->color ?? '#ccc' }}; border-color:{{ $teacher->subjects->first()->color ?? '#ccc' }}55;">
                                {{ $teacher->subjects->pluck('name_ar')->join(', ') }}
                            </span>
                        @else
                            <span style="color:#475569;">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:0.3rem;flex-wrap:wrap;">
                        @foreach($teacher->gradeLevels as $gl)
                            <span class="badge badge-gray" style="font-size:0.65rem;">{{ $gl->name }}</span>
                        @endforeach
                        </div>
                    </td>
                    <td style="color:#475569;">{{ $teacher->user->email }}</td>
                    <td style="text-align:center;">
                        <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn-secondary" style="padding:0.4rem 0.6rem;font-size:0.8rem;color:#facc15;border-color:rgba(250,204,21,0.3);">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المعلم؟');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-secondary" style="padding:0.4rem 0.6rem;font-size:0.8rem;color:#f87171;border-color:rgba(248,113,113,0.3);">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:2rem;color:#475569;">لا يوجد معلمون مسجلون حتى الآن.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($teachers->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid rgba(12, 114, 97, 0.2);">
        {{ $teachers->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
