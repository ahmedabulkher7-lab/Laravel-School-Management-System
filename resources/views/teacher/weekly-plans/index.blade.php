@extends('layouts.teacher')
@section('title', 'الجدول الأسبوعي')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">الجدول الأسبوعي للطلاب</h1>
        <div class="page-subtitle">أدخل خطة كل مادة لصفوفك بنفس نموذج الجدول الأسبوعي</div>
    </div>
    <form method="GET" action="{{ route('teacher.weekly-plans.index') }}" style="display:flex;align-items:end;gap:0.6rem;">
        @if($selectedTrack)
            <input type="hidden" name="track" value="{{ $selectedTrack }}">
        @endif
        @if($selectedGradeLevel)
            <input type="hidden" name="grade_level_id" value="{{ $selectedGradeLevel->id }}">
        @endif
        <div>
            <label class="form-label" style="font-size:0.78rem;">أسبوع يبدأ في</label>
            <input type="date" name="week_start" value="{{ $weekStart->toDateString() }}" class="form-input" style="min-width:170px;">
        </div>
        <button class="btn-secondary" type="submit"><i class="fas fa-calendar-alt"></i> عرض</button>
    </form>
</div>

<div class="alert-success" style="margin-bottom:1.25rem;">
    <i class="fas fa-info-circle"></i>
    خطة الأسبوع: {{ $weekStart->locale('ar')->isoFormat('D MMMM') }} إلى {{ $weekStart->copy()->addDays(6)->locale('ar')->isoFormat('D MMMM YYYY') }}
</div>

@if(!$selectedTrack)
    <div style="max-width:760px;margin:2rem auto;">
        <div class="card" style="text-align:center;padding:2.5rem;">
            <div style="font-size:2rem;margin-bottom:1rem;">📋</div>
            <h2 style="font-size:1.3rem;font-weight:700;color:#0C7261;margin-bottom:0.5rem;">اختر المسار الدراسي</h2>
            <p style="color:#475569;font-size:0.9rem;margin-bottom:2rem;">اختر المسار أولاً لعرض الصفوف والمواد الخاصة به</p>
            <div style="display:flex;gap:1.5rem;justify-content:center;flex-wrap:wrap;">
                @forelse($tracks as $track)
                    @php
                        $trackValue = $track->value ?? $track;
                        $isArabic = $trackValue === 'arabic';
                        $trackDone = $trackCompletion[$trackValue]['complete'] ?? false;
                    @endphp
                    <a href="{{ route('teacher.weekly-plans.index', ['track' => $trackValue, 'week_start' => $weekStart->toDateString()]) }}"
                       style="display:flex;flex-direction:column;align-items:center;gap:0.8rem;padding:1.7rem 2.8rem;border-radius:1.1rem;text-decoration:none;
                              background:{{ $trackDone ? 'linear-gradient(135deg,#fef2f2,#fecaca)' : ($isArabic ? 'linear-gradient(135deg,#ecfdf5,#d1fae5)' : 'linear-gradient(135deg,#eff6ff,#dbeafe)') }};
                              border:2px solid {{ $trackDone ? '#ef4444' : ($isArabic ? 'rgba(5,150,105,0.3)' : 'rgba(37,99,235,0.3)') }};">
                        <div style="font-size:3rem;">{{ $isArabic ? '📚' : '🌐' }}</div>
                        <strong style="color:{{ $trackDone ? '#b91c1c' : ($isArabic ? '#065f46' : '#1e40af') }};font-size:1.1rem;">
                            {{ $isArabic ? 'عربي' : 'لغات' }}
                        </strong>
                        <span style="font-size:0.78rem;color:{{ $trackDone ? '#b91c1c' : '#64748b' }};">
                            {{ $trackDone ? 'تم إكمال المسار' : $trackCompletion[$trackValue]['completed'] . ' من ' . $trackCompletion[$trackValue]['total'] . ' صف' }}
                        </span>
                    </a>
                @empty
                    <div style="color:#ef4444;">لا توجد مسارات أو صفوف مسندة إليك.</div>
                @endforelse
            </div>
        </div>
    </div>
@elseif(!$selectedGradeLevel)
    <div style="max-width:760px;margin:2rem auto;">
        <a href="{{ route('teacher.weekly-plans.index', ['week_start' => $weekStart->toDateString()]) }}"
           style="display:inline-flex;align-items:center;gap:0.4rem;color:#475569;font-size:0.88rem;text-decoration:none;margin-bottom:1.5rem;">
            <i class="fas fa-arrow-right"></i> العودة لاختيار المسار
        </a>
        <div class="card" style="text-align:center;padding:2.5rem;">
            <div style="display:inline-flex;align-items:center;gap:0.75rem;background:{{ $selectedTrack === 'arabic' ? 'rgba(5,150,105,0.1)' : 'rgba(37,99,235,0.1)' }};padding:0.5rem 1.25rem;border-radius:2rem;margin-bottom:1.5rem;">
                <span style="font-size:1.4rem;">{{ $selectedTrack === 'arabic' ? '📚' : '🌐' }}</span>
                <strong style="color:{{ $selectedTrack === 'arabic' ? '#065f46' : '#1e40af' }};">مسار {{ $selectedTrack === 'arabic' ? 'عربي' : 'لغات' }}</strong>
            </div>
            <h2 style="font-size:1.2rem;font-weight:700;color:#0C7261;margin-bottom:0.5rem;">اختر الصف الدراسي</h2>
            <p style="color:#475569;font-size:0.9rem;margin-bottom:2rem;">كل صف له جدول أسبوعي مستقل</p>
            <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                @forelse($filteredGradeLevels as $gradeLevel)
                          @php $gradeDone = $completion[$gradeLevel->id]['complete'] ?? false; @endphp
                    <a href="{{ route('teacher.weekly-plans.index', ['track' => $selectedTrack, 'grade_level_id' => $gradeLevel->id, 'week_start' => $weekStart->toDateString()]) }}"
                              style="display:flex;flex-direction:column;align-items:center;gap:0.6rem;padding:1.4rem 2rem;border-radius:1rem;text-decoration:none;min-width:150px;background:{{ $gradeDone ? '#fef2f2' : 'white' }};border:2px solid {{ $gradeDone ? '#ef4444' : 'rgba(12,114,97,0.2)' }};">
                                <i class="fas fa-chalkboard" style="font-size:1.7rem;color:{{ $gradeDone ? '#dc2626' : '#0C7261' }};"></i>
                                <strong style="color:{{ $gradeDone ? '#b91c1c' : '#1e293b' }};font-size:0.95rem;">{{ $gradeLevel->name }}</strong>
                                <span style="font-size:0.75rem;color:{{ $gradeDone ? '#b91c1c' : '#64748b' }};">{{ $gradeDone ? 'تم إكمال الصف' : $completion[$gradeLevel->id]['planned'] . ' من ' . $completion[$gradeLevel->id]['total'] . ' مادة' }}</span>
                    </a>
                @empty
                    <div style="color:#ef4444;">لا توجد صفوف في هذا المسار.</div>
                @endforelse
            </div>
        </div>
    </div>
@else
    @php $subjects = $selectedGradeLevel->subjects->whereIn('id', $teacherSubjectIds); @endphp
    @if($subjects->isEmpty())
        <div class="alert-error" style="justify-content:center;">لا توجد مواد مسندة إليك في هذا الصف.</div>
    @else
        <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;flex-wrap:wrap;">
            <a href="{{ route('teacher.weekly-plans.index', ['week_start' => $weekStart->toDateString()]) }}" class="btn-secondary" style="padding:0.4rem 0.8rem;font-size:0.8rem;">
                <i class="fas fa-th"></i> المسارات
            </a>
            <i class="fas fa-chevron-left" style="color:#cbd5e1;font-size:0.7rem;"></i>
            <a href="{{ route('teacher.weekly-plans.index', ['track' => $selectedTrack, 'week_start' => $weekStart->toDateString()]) }}" class="btn-secondary" style="padding:0.4rem 0.8rem;font-size:0.8rem;">
                {{ $selectedTrack === 'arabic' ? 'عربي' : 'لغات' }}
            </a>
            <i class="fas fa-chevron-left" style="color:#cbd5e1;font-size:0.7rem;"></i>
            <span style="background:#0C7261;color:white;padding:0.4rem 0.8rem;border-radius:0.5rem;font-size:0.8rem;font-weight:700;">{{ $selectedGradeLevel->name }}</span>
        </div>
        @php $selectedGradeDone = $completion[$selectedGradeLevel->id]['complete'] ?? false; @endphp
        <form method="POST" action="{{ route('teacher.weekly-plans.store', $selectedGradeLevel) }}" class="card" style="margin-bottom:1.5rem;overflow:hidden;border:2px solid {{ $selectedGradeDone ? '#ef4444' : 'rgba(12,114,97,0.16)' }};">
            @csrf
            <input type="hidden" name="week_start" value="{{ $weekStart->toDateString() }}">
            <div class="card-header" style="background:{{ $selectedGradeDone ? '#fef2f2' : 'rgba(12,114,97,0.06)' }};">
                <div class="card-title" style="display:flex;align-items:center;gap:0.6rem;">
                    <i class="fas fa-chalkboard" style="color:{{ $selectedGradeDone ? '#dc2626' : '#0C7261' }};"></i>
                    {{ $selectedGradeLevel->name }}
                    <span style="font-size:0.75rem;color:#64748b;font-weight:500;">{{ $subjects->count() }} مادة</span>
                    @if($selectedGradeDone)
                        <span style="font-size:0.72rem;color:#b91c1c;font-weight:700;">تم إكمال الصف</span>
                    @endif
                </div>
                <button type="submit" class="btn-primary" style="padding:0.45rem 0.9rem;font-size:0.82rem;">
                    <i class="fas fa-save"></i> حفظ خطة الصف
                </button>
            </div>
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table style="width:100%;min-width:700px;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#0C7261;color:white;">
                            <th style="padding:0.75rem;text-align:right;width:18%;">المادة</th>
                            <th style="padding:0.75rem;text-align:right;">Class Work<br><small style="font-weight:400;">عمل الفصل</small></th>
                            <th style="padding:0.75rem;text-align:right;">Homework<br><small style="font-weight:400;">الواجب</small></th>
                            <th style="padding:0.75rem;text-align:right;">Online games<br><small style="font-weight:400;">ألعاب أونلاين</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $subject)
                            @php $plan = $plans->get($selectedGradeLevel->id . '-' . $subject->id); @endphp
                            <tr style="border-bottom:1px solid #e2e8f0;vertical-align:top;">
                                <td style="padding:0.85rem;font-weight:700;color:#0C7261;">
                                    {{ $subject->name_ar ?? $subject->name }}
                                    <input type="hidden" name="plans[{{ $loop->index }}][subject_id]" value="{{ $subject->id }}">
                                </td>
                                <td style="padding:0.6rem;"><textarea name="plans[{{ $loop->index }}][class_work]" class="form-input" rows="3" placeholder="مثال: Unit 4 lesson 1">{{ old("plans.{$loop->index}.class_work", $plan?->class_work) }}</textarea></td>
                                <td style="padding:0.6rem;"><textarea name="plans[{{ $loop->index }}][homework]" class="form-input" rows="3" placeholder="اكتب الواجب">{{ old("plans.{$loop->index}.homework", $plan?->homework) }}</textarea></td>
                                <td style="padding:0.6rem;"><textarea name="plans[{{ $loop->index }}][online_games]" class="form-input" rows="3" placeholder="اكتب النشاط أو اللعبة">{{ old("plans.{$loop->index}.online_games", $plan?->online_games) }}</textarea></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    @endif
@endif
@endsection
