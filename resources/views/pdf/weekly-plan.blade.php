<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الجدول الأسبوعي - {{ $gradeLevel->name }}</title>
    <style>
        @page { margin: 28px 35px; }
        body { font-family: 'xbriyaz', sans-serif; direction: rtl; color:#222; font-size:12px; }
        .header { text-align:center; border-bottom:2px solid #0C7261; padding-bottom:10px; margin-bottom:16px; }
        .logo { width:58px; height:58px; object-fit:contain; }
        h1 { color:#0C7261; font-size:20px; margin:3px 0; }
        .meta { color:#475569; font-size:11px; }
        .info { width:100%; margin-bottom:14px; border-collapse:collapse; }
        .info td { background:#f1f5f9; border:1px solid #cbd5e1; padding:7px; }
        .label { color:#475569; font-weight:bold; width:110px; }
        table.plan { width:100%; border-collapse:collapse; table-layout:fixed; }
        .plan th { background:#0C7261; color:#fff; padding:8px 6px; border:1px solid #075e51; font-size:11px; }
        .plan td { padding:7px 6px; border:1px solid #94a3b8; vertical-align:top; font-size:10px; line-height:1.5; word-wrap:break-word; }
        .subject { width:15%; background:#e2e8f0; font-weight:bold; color:#0C7261; }
        .cell { width:28.33%; }
        .teacher { color:#475569; font-size:9px; font-weight:bold; }
        .footer { margin-top:20px; text-align:center; color:#64748b; font-size:10px; border-top:1px solid #0C7261; padding-top:8px; }
    </style>
</head>
<body>
    <div class="header">
        <img class="logo" src="{{ public_path('images/logo.png') }}" alt="Summit">
        <h1>SUMMIT ONLINE SCHOOL</h1>
        <div style="color:#dc2626;font-weight:bold;">الجدول الأسبوعي - {{ $gradeLevel->name }}</div>
        <div class="meta">للفترة من {{ $weekStart->format('d/m/Y') }} إلى {{ $weekEnd->format('d/m/Y') }}</div>
    </div>

    <table class="info">
        <tr>
            <td class="label">الصف الدراسي</td>
            <td>{{ $gradeLevel->name }}</td>
            <td class="label">المسار</td>
            <td>{{ ($gradeLevel->track->value ?? $gradeLevel->track) === 'arabic' ? 'عربي' : 'لغات' }}</td>
        </tr>
    </table>

    <table class="plan">
        <thead>
            <tr>
                <th class="subject">Sub.<br>المادة</th>
                <th>Class Work<br>عمل الفصل</th>
                <th>Homework<br>الواجب</th>
                <th>Online games<br>ألعاب أونلاين</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary['rows'] as $row)
                @php
                    $values = ['class_work' => [], 'homework' => [], 'online_games' => []];
                    foreach ($row['plans'] as $plan) {
                        $teacherName = $plan->teacher?->full_name ? $plan->teacher->full_name . ': ' : '';
                        foreach (array_keys($values) as $field) {
                            if ($plan->{$field}) {
                                $values[$field][] = $teacherName . $plan->{$field};
                            }
                        }
                    }
                @endphp
                <tr>
                    <td class="subject">{{ $row['subject']->name_ar ?? $row['subject']->name }}</td>
                    <td class="cell">{!! implode('<br><br>', array_map(fn ($value) => e($value), $values['class_work'])) ?: '-' !!}</td>
                    <td class="cell">{!! implode('<br><br>', array_map(fn ($value) => e($value), $values['homework'])) ?: '-' !!}</td>
                    <td class="cell">{!! implode('<br><br>', array_map(fn ($value) => e($value), $values['online_games'])) ?: '-' !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">تم اعتماد الجدول بعد تسجيل خطط جميع المدرسين المسؤولين عن الصف</div>
</body>
</html>
