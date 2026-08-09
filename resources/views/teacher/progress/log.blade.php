@extends('layouts.teacher')
@section('title', 'تسجيل التقدم اليومي')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">تسجيل التقدم اليومي</h1>
        <div class="page-subtitle">اختر المسار والصف الدراسي أولاً، ثم ابدأ بإدخال سجلات الطلاب</div>
    </div>
    <div style="font-size:0.85rem;color:#475569;">
        <i class="fas fa-calendar-alt"></i>
        {{ now()->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}
    </div>
</div>

{{-- Step 1: Track Selection --}}
@if(!$selectedTrack)
    <div style="max-width:680px;margin:2rem auto;">
        <div class="card" style="text-align:center;padding:2.5rem;">
            <div style="font-size:2rem;margin-bottom:1rem;">📋</div>
            <h2 style="font-size:1.3rem;font-weight:700;color:#0C7261;margin-bottom:0.5rem;">اختر المسار الدراسي</h2>
            <p style="color:#475569;font-size:0.9rem;margin-bottom:2rem;">ستقوم بإدخال سجلات التقدم اليومي لطلاب المسار المختار</p>
            
            <div style="display:flex;gap:1.5rem;justify-content:center;flex-wrap:wrap;">
                @forelse($tracks as $track)
                    @php
                        $trackObj = \App\Enums\StudyTrack::from($track->value ?? $track);
                        $isArabic = ($trackObj->value === 'arabic');
                    @endphp
                    <a href="{{ route('teacher.progress.log', ['track' => $trackObj->value]) }}"
                       style="display:flex;flex-direction:column;align-items:center;gap:1rem;
                              padding:2rem 3rem;border-radius:1.25rem;text-decoration:none;
                              background:{{ $isArabic ? 'linear-gradient(135deg, #ecfdf5, #d1fae5)' : 'linear-gradient(135deg, #eff6ff, #dbeafe)' }};
                              border:2px solid {{ $isArabic ? 'rgba(5,150,105,0.3)' : 'rgba(37,99,235,0.3)' }};
                              transition:all 0.3s;box-shadow:0 2px 12px rgba(0,0,0,0.06);"
                       onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)'"
                       onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                        <div style="font-size:3.5rem;">{{ $isArabic ? '📚' : '🌐' }}</div>
                        <div>
                            <div style="font-size:1.25rem;font-weight:800;color:{{ $isArabic ? '#065f46' : '#1e40af' }};">
                                {{ $trackObj->label() }}
                            </div>
                            @php $count = $filteredGradeLevels->count(); @endphp
                            <div style="font-size:0.8rem;color:#64748b;margin-top:0.25rem;">
                                {{ $gradeLevels->where('track', $trackObj->value)->count() }} صف دراسي
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="color:#ef4444;font-size:1rem;">
                        <i class="fas fa-exclamation-circle"></i>
                        لم يتم تعيينك لأي صف دراسي بعد. يرجى التواصل مع المدير.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

{{-- Step 2: Grade Level Selection --}}
@elseif(!$selectedGradeLevel)
    @php $trackObj = \App\Enums\StudyTrack::from($selectedTrack); @endphp
    <div style="max-width:680px;margin:2rem auto;">
        
        <a href="{{ route('teacher.progress.log') }}" 
           style="display:inline-flex;align-items:center;gap:0.4rem;color:#475569;font-size:0.88rem;text-decoration:none;margin-bottom:1.5rem;">
            <i class="fas fa-arrow-right"></i> العودة لاختيار المسار
        </a>

        <div class="card" style="text-align:center;padding:2.5rem;">
            <div style="display:inline-flex;align-items:center;gap:0.75rem;background:{{ $trackObj->value === 'arabic' ? 'rgba(5,150,105,0.1)' : 'rgba(37,99,235,0.1)' }};padding:0.5rem 1.25rem;border-radius:2rem;margin-bottom:1.5rem;">
                <span style="font-size:1.4rem;">{{ $trackObj->value === 'arabic' ? '📚' : '🌐' }}</span>
                <span style="font-weight:700;color:{{ $trackObj->value === 'arabic' ? '#065f46' : '#1e40af' }};font-size:1rem;">مسار {{ $trackObj->label() }}</span>
            </div>
            
            <h2 style="font-size:1.2rem;font-weight:700;color:#0C7261;margin-bottom:0.5rem;">اختر الصف الدراسي</h2>
            <p style="color:#475569;font-size:0.9rem;margin-bottom:2rem;">اختر الصف الذي ستسجل له التقدم اليومي الآن</p>
            
            <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                @forelse($filteredGradeLevels as $gl)
                    <a href="{{ route('teacher.progress.log', ['track' => $selectedTrack, 'grade_level_id' => $gl->id]) }}"
                       style="display:flex;flex-direction:column;align-items:center;gap:0.6rem;
                              padding:1.5rem 2rem;border-radius:1rem;text-decoration:none;min-width:140px;
                              background:white;border:2px solid rgba(12,114,97,0.2);
                              transition:all 0.3s;box-shadow:0 2px 8px rgba(0,0,0,0.06);"
                       onmouseover="this.style.borderColor='#0C7261';this.style.background='rgba(12,114,97,0.06)';this.style.transform='translateY(-3px)'"
                       onmouseout="this.style.borderColor='rgba(12,114,97,0.2)';this.style.background='white';this.style.transform='translateY(0)'">
                        <i class="fas fa-chalkboard" style="font-size:1.75rem;color:#0C7261;"></i>
                        <div style="font-weight:700;color:#1e293b;font-size:0.95rem;">{{ $gl->name }}</div>
                        <div style="font-size:0.75rem;color:#64748b;">
                            {{ $gl->students()->count() }} طالب
                        </div>
                    </a>
                @empty
                    <div style="color:#ef4444;">لا توجد صفوف دراسية في هذا المسار.</div>
                @endforelse
            </div>
        </div>
    </div>

{{-- Step 3: Show Students for Progress Logging --}}
@else
    @php $trackObj = \App\Enums\StudyTrack::from($selectedTrack); @endphp

    {{-- Breadcrumb / navigation --}}
    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
        <a href="{{ route('teacher.progress.log') }}"
           style="display:inline-flex;align-items:center;gap:0.4rem;color:#475569;font-size:0.85rem;text-decoration:none;
                  padding:0.3rem 0.8rem;background:white;border:1px solid #e2e8f0;border-radius:0.5rem;">
            <i class="fas fa-th"></i> المسارات
        </a>
        <i class="fas fa-chevron-left" style="color:#cbd5e1;font-size:0.7rem;"></i>
        <a href="{{ route('teacher.progress.log', ['track' => $selectedTrack]) }}"
           style="display:inline-flex;align-items:center;gap:0.4rem;color:#475569;font-size:0.85rem;text-decoration:none;
                  padding:0.3rem 0.8rem;background:white;border:1px solid #e2e8f0;border-radius:0.5rem;">
            {{ $trackObj->label() }}
        </a>
        <i class="fas fa-chevron-left" style="color:#cbd5e1;font-size:0.7rem;"></i>
        <span style="padding:0.3rem 0.8rem;background:#0C7261;color:white;border-radius:0.5rem;font-size:0.85rem;font-weight:600;">
            {{ $selectedGradeLevel->name }}
        </span>
    </div>

    {{-- Summary info --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;gap:0.75rem;">
            <div style="background:{{ $trackObj->value === 'arabic' ? 'rgba(5,150,105,0.1)' : 'rgba(37,99,235,0.1)' }};
                        padding:0.5rem 1rem;border-radius:2rem;font-size:0.85rem;font-weight:600;
                        color:{{ $trackObj->value === 'arabic' ? '#065f46' : '#1e40af' }};">
                {{ $trackObj->value === 'arabic' ? '📚' : '🌐' }} مسار {{ $trackObj->label() }} — {{ $selectedGradeLevel->name }}
            </div>
            <div style="color:#64748b;font-size:0.85rem;">
                {{ $students->count() }} طالب
            </div>
        </div>
    </div>

    @if($students->isEmpty())
        <div class="alert-error" style="justify-content:center;">
            <i class="fas fa-users-slash"></i>
            لا يوجد طلاب مسجلون في هذا الصف بعد.
        </div>
    @else
        <div class="grid-2">
            @foreach($students as $student)
                @livewire('teacher.daily-progress-log', [
                    'studentId'   => $student->id,
                    'teacherId'   => $teacher->id,
                ], key($student->id))
            @endforeach
        </div>
    @endif
@endif

@endsection
