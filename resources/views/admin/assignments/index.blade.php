@extends('layouts.admin')
@section('title', 'تعيينات المعلمين للطلاب')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">تعيين المعلمين للطلاب</h1>
        <div class="page-subtitle">ربط طالب معين بمعلم في مادة محددة للمتابعة الفردية</div>
    </div>
</div>

<div class="grid-2" style="align-items:start;">
    <!-- Form -->
    <div class="card">
        <div class="card-header"><span class="card-title">إضافة تعيين جديد</span></div>
        <div class="card-body">
            <form action="{{ route('admin.assignments.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">المعلم والمادة</label>
                    <select name="teacher_id" id="teacher_select" class="form-select" required onchange="updateSubjects()">
                        <option value="">-- اختر المعلم --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" data-subject="{{ $teacher->subject_id }}">
                                {{ $teacher->full_name }} ({{ $teacher->subject->name_ar ?? $teacher->subject->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="subject_id" id="subject_id">

                <div class="form-group">
                    <label class="form-label">الطالب</label>
                    <select name="student_id" class="form-select" required>
                        <option value="">-- اختر الطالب --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->full_name }} - {{ $student->gradeLevel->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-top:1rem;">
                    <i class="fas fa-link"></i> ربط المعلم بالطالب
                </button>
            </form>
            <script>
                function updateSubjects() {
                    const select = document.getElementById('teacher_select');
                    const subjectId = select.options[select.selectedIndex].getAttribute('data-subject');
                    document.getElementById('subject_id').value = subjectId;
                }
            </script>
        </div>
    </div>

    <!-- List -->
    <div class="card">
        <div class="card-header"><span class="card-title">التعيينات الحالية</span></div>
        <div class="card-body" style="padding:0;max-height:500px;overflow-y:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>المعلم (المادة)</th>
                        <th>الطالب</th>
                        <th style="text-align:center;">حذف</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assign)
                    <tr>
                        <td style="color:#0C7261;">
                            {{ $assign->teacher->full_name }}<br>
                            <span style="font-size:0.75rem;color:#475569;">{{ $assign->subject->name_ar ?? $assign->subject->name }}</span>
                        </td>
                        <td>{{ $assign->student->full_name }}</td>
                        <td style="text-align:center;">
                            <form action="{{ route('admin.assignments.destroy', $assign) }}" method="POST" onsubmit="return confirm('إلغاء التعيين؟');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-secondary" style="padding:0.3rem 0.5rem;font-size:0.75rem;color:#f87171;border-color:rgba(248,113,113,0.3);">
                                    <i class="fas fa-unlink"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;padding:1.5rem;">لا يوجد تعيينات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
