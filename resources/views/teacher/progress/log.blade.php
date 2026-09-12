@extends('layouts.teacher')
@section('title', 'تسجيل التقدم اليومي')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">تسجيل التقدم اليومي</h1>
        <div class="page-subtitle">اختر المادة أولاً، ثم المسار والصف الدراسي لإدخال تقييمات الطلاب</div>
    </div>
    <div style="font-size:0.85rem;color:#475569;">
        <i class="fas fa-calendar-alt"></i>
        {{ now()->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}
    </div>
</div>

{{-- Step 1: Subject selection --}}
@if(!$selectedSubject)
    <div style="max-width:760px;margin:2rem auto;">
        <div class="card" style="text-align:center;padding:2.5rem;">
            <div style="font-size:2rem;margin-bottom:1rem;">📋</div>
            <h2 style="font-size:1.3rem;font-weight:700;color:#0C7261;margin-bottom:0.5rem;">اختر المادة</h2>
            <p style="color:#475569;font-size:0.9rem;margin-bottom:2rem;">اختر المادة التي ستُسجّل لها تقييم الطلاب، ثم حدّد المسار والصف</p>

            <div style="display:flex;gap:1.25rem;justify-content:center;flex-wrap:wrap;">
                @forelse($subjects as $subject)
                    @php
                        $subjectStatus = $subjectCompletion->get($subject->id, ['total' => 0, 'completed' => 0, 'complete' => false]);
                        $subjectDone = $subjectStatus['complete'];
                    @endphp
                    <a href="{{ route('teacher.progress.log', ['subject_id' => $subject->id]) }}"
                       style="display:flex;flex-direction:column;align-items:center;gap:0.8rem;
                              padding:1.6rem 2.4rem;border-radius:1.25rem;text-decoration:none;min-width:170px;
                              background:{{ $subjectDone ? 'linear-gradient(135deg,#fef2f2,#fecaca)' : 'linear-gradient(135deg,#ecfdf5,#d1fae5)' }};
                              border:2px solid {{ $subjectDone ? '#ef4444' : 'rgba(5,150,105,0.3)' }};transition:all .3s;
                              box-shadow:0 2px 12px rgba(0,0,0,.06);"
                       onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.12)'"
                       onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 12px rgba(0,0,0,.06)'">
                        <i class="fas {{ $subjectDone ? 'fa-check-circle' : 'fa-book' }}" style="font-size:2.2rem;color:{{ $subjectDone ? '#dc2626' : '#065f46' }};"></i>
                        <strong style="font-size:1.05rem;color:{{ $subjectDone ? '#b91c1c' : '#065f46' }};">{{ $subject->name_ar ?? $subject->name }}</strong>
                        <span style="font-size:.72rem;color:{{ $subjectDone ? '#b91c1c' : '#64748b' }};">{{ $subjectDone ? 'تم تقييم المادة' : $subjectStatus['completed'] . ' من ' . $subjectStatus['total'] . ' طالب' }}</span>
                    </a>
                @empty
                    <div style="text-align:center;padding:1.5rem;color:#64748b;">
                        <i class="fas fa-calendar-check fa-2x" style="color:#0C7261;margin-bottom:0.75rem;display:block;"></i>
                        <div style="font-weight:700;color:#1e293b;margin-bottom:0.25rem;">لا توجد حصص مجدولة لك اليوم لتسجيل تقييماتها</div>
                        <div style="font-size:0.85rem;">تظهر هنا فقط المواد والصفوف التي لديك حصص مجدولة معها في جدول اليوم.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

{{-- Step 2: Track selection --}}
@elseif(!$selectedTrack)
    <div style="max-width:760px;margin:2rem auto;">
        <a href="{{ route('teacher.progress.log') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;color:#475569;font-size:.88rem;text-decoration:none;margin-bottom:1.5rem;">
            <i class="fas fa-arrow-right"></i> العودة لاختيار المادة
        </a>
        <div class="card" style="text-align:center;padding:2.5rem;">
            <div style="display:inline-flex;align-items:center;gap:.75rem;background:rgba(5,150,105,.1);padding:.5rem 1.25rem;border-radius:2rem;margin-bottom:1.5rem;">
                <i class="fas fa-book" style="color:#065f46;"></i>
                <strong style="color:#065f46;">{{ $selectedSubject->name_ar ?? $selectedSubject->name }}</strong>
            </div>
            <h2 style="font-size:1.2rem;font-weight:700;color:#0C7261;margin-bottom:.5rem;">اختر المسار الدراسي</h2>
            <p style="color:#475569;font-size:.9rem;margin-bottom:2rem;">تظهر فقط المسارات التي تُدرّس فيها هذه المادة</p>

            <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                @forelse($tracks as $track)
                    @php
                        $isArabic = $track === 'arabic';
                        $trackStatus = $trackCompletion->get($track, ['total' => 0, 'completed' => 0, 'complete' => false]);
                        $trackDone = $trackStatus['complete'];
                    @endphp
                    <a href="{{ route('teacher.progress.log', ['subject_id' => $selectedSubject->id, 'track' => $track]) }}"
                       style="display:flex;flex-direction:column;align-items:center;gap:.6rem;padding:1.5rem 2.3rem;border-radius:1rem;text-decoration:none;min-width:160px;
                              background:{{ $trackDone ? 'linear-gradient(135deg,#fef2f2,#fecaca)' : ($isArabic ? 'linear-gradient(135deg,#ecfdf5,#d1fae5)' : 'linear-gradient(135deg,#eff6ff,#dbeafe)') }};
                              border:2px solid {{ $trackDone ? '#ef4444' : ($isArabic ? 'rgba(5,150,105,.3)' : 'rgba(37,99,235,.3)') }};transition:all .3s;box-shadow:0 2px 8px rgba(0,0,0,.06);"
                       onmouseover="this.style.transform='translateY(-3px)'"
                       onmouseout="this.style.transform='translateY(0)'">
                        <i class="fas {{ $trackDone ? 'fa-check-circle' : ($isArabic ? 'fa-book-open' : 'fa-globe') }}" style="font-size:1.75rem;color:{{ $trackDone ? '#dc2626' : ($isArabic ? '#065f46' : '#1e40af') }};"></i>
                        <strong style="color:{{ $trackDone ? '#b91c1c' : ($isArabic ? '#065f46' : '#1e40af') }};">{{ $isArabic ? 'عربي' : 'لغات' }}</strong>
                        <span style="font-size:.75rem;color:{{ $trackDone ? '#b91c1c' : '#64748b' }};">{{ $trackDone ? 'تم تقييم التراك' : $trackStatus['completed'] . ' من ' . $trackStatus['total'] . ' طالب' }}</span>
                    </a>
                @empty
                    <div style="color:#ef4444;">لا توجد مسارات متاحة لهذه المادة.</div>
                @endforelse
            </div>
        </div>
    </div>

{{-- Step 3: Grade level selection --}}
@elseif(!$selectedGradeLevel)
    @php $trackObj = \App\Enums\StudyTrack::from($selectedTrack); @endphp
    <div style="max-width:760px;margin:2rem auto;">
        <a href="{{ route('teacher.progress.log', ['subject_id' => $selectedSubject->id]) }}"
           style="display:inline-flex;align-items:center;gap:.4rem;color:#475569;font-size:.88rem;text-decoration:none;margin-bottom:1.5rem;">
            <i class="fas fa-arrow-right"></i> العودة لاختيار المسار
        </a>
        <div class="card" style="text-align:center;padding:2.5rem;">
            <h2 style="font-size:1.2rem;font-weight:700;color:#0C7261;margin-bottom:.5rem;">اختر الصف الدراسي</h2>
            <p style="color:#475569;font-size:.9rem;margin-bottom:2rem;">{{ $selectedSubject->name_ar ?? $selectedSubject->name }} — مسار {{ $trackObj->label() }}</p>
            <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                @forelse($filteredGradeLevels as $gradeLevel)
                    @php
                        $gradeStatus = $gradeCompletion->get($selectedSubject->id . '-' . $gradeLevel->id, ['total' => 0, 'completed' => 0, 'complete' => false]);
                        $gradeDone = $gradeStatus['complete'];
                    @endphp
                    <a href="{{ route('teacher.progress.log', ['subject_id' => $selectedSubject->id, 'track' => $selectedTrack, 'grade_level_id' => $gradeLevel->id]) }}"
                       style="display:flex;flex-direction:column;align-items:center;gap:.6rem;padding:1.5rem 2rem;border-radius:1rem;text-decoration:none;min-width:150px;background:{{ $gradeDone ? '#fef2f2' : 'white' }};border:2px solid {{ $gradeDone ? '#ef4444' : 'rgba(12,114,97,.2)' }};transition:all .3s;box-shadow:0 2px 8px rgba(0,0,0,.06);"
                       onmouseover="this.style.borderColor='{{ $gradeDone ? '#dc2626' : '#0C7261' }}';this.style.transform='translateY(-3px)'"
                       onmouseout="this.style.borderColor='{{ $gradeDone ? '#ef4444' : 'rgba(12,114,97,.2)' }}';this.style.transform='translateY(0)'">
                        <i class="fas {{ $gradeDone ? 'fa-check-circle' : 'fa-chalkboard' }}" style="font-size:1.75rem;color:{{ $gradeDone ? '#dc2626' : '#0C7261' }};"></i>
                        <strong style="color:{{ $gradeDone ? '#b91c1c' : '#1e293b' }};font-size:.95rem;">{{ $gradeLevel->name }}</strong>
                        <span style="font-size:.75rem;color:{{ $gradeDone ? '#b91c1c' : '#64748b' }};">{{ $gradeDone ? 'تم تقييم الصف' : $gradeStatus['completed'] . ' من ' . $gradeStatus['total'] . ' طالب' }}</span>
                    </a>
                @empty
                    <div style="color:#ef4444;">لا توجد صفوف في هذا المسار لهذه المادة.</div>
                @endforelse
            </div>
        </div>
    </div>

{{-- Step 4: Students' progress forms --}}
@else
    @php $trackObj = \App\Enums\StudyTrack::from($selectedTrack); @endphp

    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
        <a href="{{ route('teacher.progress.log') }}" style="display:inline-flex;align-items:center;gap:.4rem;color:#475569;font-size:.85rem;text-decoration:none;padding:.3rem .8rem;background:white;border:1px solid #e2e8f0;border-radius:.5rem;">
            <i class="fas fa-book"></i> المواد
        </a>
        <i class="fas fa-chevron-left" style="color:#cbd5e1;font-size:.7rem;"></i>
        <a href="{{ route('teacher.progress.log', ['subject_id' => $selectedSubject->id]) }}" style="display:inline-flex;align-items:center;gap:.4rem;color:#475569;font-size:.85rem;text-decoration:none;padding:.3rem .8rem;background:white;border:1px solid #e2e8f0;border-radius:.5rem;">
            {{ $selectedSubject->name_ar ?? $selectedSubject->name }}
        </a>
        <i class="fas fa-chevron-left" style="color:#cbd5e1;font-size:.7rem;"></i>
        <a href="{{ route('teacher.progress.log', ['subject_id' => $selectedSubject->id, 'track' => $selectedTrack]) }}" style="display:inline-flex;align-items:center;gap:.4rem;color:#475569;font-size:.85rem;text-decoration:none;padding:.3rem .8rem;background:white;border:1px solid #e2e8f0;border-radius:.5rem;">
            {{ $trackObj->label() }}
        </a>
        <i class="fas fa-chevron-left" style="color:#cbd5e1;font-size:.7rem;"></i>
        <span style="padding:.3rem .8rem;background:#0C7261;color:white;border-radius:.5rem;font-size:.85rem;font-weight:600;">{{ $selectedGradeLevel->name }}</span>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <div style="background:{{ $trackObj->value === 'arabic' ? 'rgba(5,150,105,.1)' : 'rgba(37,99,235,.1)' }};padding:.5rem 1rem;border-radius:2rem;font-size:.85rem;font-weight:600;color:{{ $trackObj->value === 'arabic' ? '#065f46' : '#1e40af' }};">
                {{ $selectedSubject->name_ar ?? $selectedSubject->name }} — مسار {{ $trackObj->label() }} — {{ $selectedGradeLevel->name }}
            </div>
            <div style="color:#64748b;font-size:.85rem;">{{ $students->count() }} طالب</div>
        </div>
    </div>

    @if($students->isEmpty())
        <div class="alert-error" style="justify-content:center;">
            <i class="fas fa-users-slash"></i> لا يوجد طلاب مسجلون في هذا الصف بعد.
        </div>
    @else
        <div class="grid-2">
            @foreach($students as $student)
                @livewire('teacher.daily-progress-log', [
                    'studentId' => $student->id,
                    'teacherId' => $teacher->id,
                    'subjectId' => $selectedSubject->id,
                    'scheduleId' => $selectedSchedule?->id,
                ], key($student->id . '-' . $selectedSubject->id . '-' . ($selectedSchedule?->id ?? 0)))
            @endforeach
        </div>
    @endif
@endif
@endsection
