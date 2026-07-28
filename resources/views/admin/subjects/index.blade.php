@extends('layouts.admin')
@section('title', 'المواد الدراسية')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">إدارة المواد الدراسية</h1>
        <div class="page-subtitle">المناهج والمواد المتوفرة في المدرسة</div>
    </div>
    <a href="{{ route('admin.subjects.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> إضافة مادة جديدة
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>اسم المادة</th>
                    <th>الاسم بالإنجليزية</th>
                    <th>اللون التعريفي</th>
                    <th>المراحل الدراسية المرتبطة</th>
                    <th style="text-align:center;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $subject)
                <tr>
                    <td style="font-weight:600;color:#0C7261;">{{ $subject->name_ar ?? '—' }}</td>
                    <td style="color:#475569;">{{ $subject->name }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.5rem;">
                            <div style="width:20px;height:20px;border-radius:4px;background:{{ $subject->color }};"></div>
                            <span style="font-size:0.8rem;color:#475569;">{{ $subject->color }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;gap:0.3rem;flex-wrap:wrap;">
                        @foreach($subject->gradeLevels as $gl)
                            <span class="badge badge-gray" style="font-size:0.65rem;">{{ $gl->name }}</span>
                        @endforeach
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn-secondary" style="padding:0.4rem 0.6rem;font-size:0.8rem;color:#facc15;border-color:rgba(250,204,21,0.3);">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذه المادة؟');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-secondary" style="padding:0.4rem 0.6rem;font-size:0.8rem;color:#f87171;border-color:rgba(248,113,113,0.3);">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:2rem;color:#475569;">لا توجد مواد مسجلة حتى الآن.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subjects->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid rgba(12, 114, 97, 0.2);">
        {{ $subjects->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
