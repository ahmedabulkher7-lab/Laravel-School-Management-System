@extends('layouts.student')
@section('title', 'أدائي الدراسي')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">أدائي الدراسي</h1>
        <div class="page-subtitle">الرسوم البيانية وتفاصيل التقييمات لكل مادة</div>
    </div>
</div>

@if($progress->isEmpty())
    <div class="card"><div class="card-body" style="text-align:center;color:#475569;padding:3rem;">لا توجد بيانات كافية لعرض الأداء.</div></div>
@else
    <div class="grid-2">
    @foreach($progress as $subjectId => $records)
        @php
            $subject = $records->first()->subject;
            $dates = $records->pluck('date')->map->format('m/d')->toJson();
            $scores = $records->pluck('score')->toJson();
            $color = $subject->color ?? '#f59e0b';
        @endphp
        <div class="card">
            <div class="card-header" style="border-bottom:none;padding-bottom:0;">
                <span class="card-title" style="display:flex;align-items:center;gap:0.5rem;">
                    <div style="width:12px;height:12px;border-radius:50%;background:{{ $color }};"></div>
                    {{ $subject->name_ar ?? $subject->name }}
                </span>
            </div>
            <div class="card-body">
                <canvas id="chart-{{ $subjectId }}" style="width:100%;height:200px;"></canvas>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('chart-{{ $subjectId }}').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: {!! $dates !!},
                            datasets: [{
                                label: 'الدرجة من 10',
                                data: {!! $scores !!},
                                borderColor: '{{ $color }}',
                                backgroundColor: '{{ $color }}33',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { min: 0, max: 10, grid: { color: 'rgba(12, 114, 97, 0.2)' }, ticks: { color: '#475569' } },
                                x: { grid: { color: 'rgba(12, 114, 97, 0.2)' }, ticks: { color: '#475569' } }
                            },
                            plugins: { legend: { display: false } }
                        }
                    });
                });
            </script>
        </div>
    @endforeach
    </div>
@endif
@endsection
