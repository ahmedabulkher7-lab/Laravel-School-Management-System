<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>التقرير الأسبوعي - {{ $student->full_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            background-color: #F4F1EB;
            margin: 48mm 12mm 15mm;
            header: html_reportHeader;
            footer: html_reportFooter;
        }

        * { box-sizing: border-box; }

 body { 
    background: #F4F1EB; 
    color: #004AAD; 
    direction: rtl; 
    font-family: Alexandria, sans-serif; 
    font-size: 10px; 
    font-weight: 800;
    line-height: 1.4; 
}
        .header-table,
        .student-table,
        .subject-heading,
        .data-table,
        .footer-table {
            border-collapse: collapse;
            width: 100%;
        }

        .header-spacer { height: 8mm; }
        .header-table { margin-bottom: 3mm; }
        .header-table td { vertical-align: middle; }

        .brand-logo {
            height: 27mm;
            object-fit: contain;
            vertical-align: middle;
            width: 27mm;
        }

        .brand-name {
            color: #004AAD;
            font-family: Alexandria, sans-serif;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: .2px;
            line-height: 1;
        }

        .brand-subtitle {
            color: #004AAD;
            font-family: Alexandria, sans-serif;
            font-size: 7px;
            letter-spacing: 3px;
            margin-top: 3px;
        }

        .report-title {
            color: #004AAD;
            font-size: 21px;
            font-weight: bold;
            line-height: 1.1;
            text-align: left;
        }

        .report-subtitle {
            color: #004AAD;
            font-family: Alexandria, sans-serif;
            font-size: 10px;
            letter-spacing: 2px;
            margin-top: 3px;
            text-align: left;
        }

        .top-rule {
            background: #004AAD;
            height: 1.2px;
            margin: 0 4mm;
        }

        .student-wrap {
            border: 1.2px solid #004AAD;
            border-radius: 4mm;
            margin-bottom: 8mm;
            overflow: hidden;
            padding: 4mm 1.5mm;
        }

        .student-table td {
            border-left: .7px solid #8BAED8;
            padding: 1mm 2mm;
            text-align: center;
            vertical-align: middle;
        }

        .student-table td:last-child { border-left: 0; }

        .student-label {
            color: #004AAD;
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 1.5mm;
        }

        .student-value {
            color: #004AAD;
            font-size: 15px;
            font-weight: bold;
            line-height: 1.25;
            word-wrap: break-word;
        }

        .subject-block {
            margin-bottom: 7mm;
            page-break-inside: avoid;
        }

        .subject-heading td {
            background: #004AAD;
            color: #F4F1EB;
            padding: 3.4mm 5mm;
            vertical-align: middle;
        }

        .subject-heading .subject-name {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
        }

        .subject-heading .subject-english {
            color: #F4F1EB;
            font-family: Alexandria, sans-serif;
            font-size: 8px;
            letter-spacing: 1.3px;
            text-align: left;
        }

        .data-table {
            border: 1.1px solid #004AAD;
            table-layout: fixed;
        }

        .data-table th {
            background: #E8EEF6;
            border-left: .7px solid #8BAED8;
            border-bottom: .8px solid #004AAD;
            color: #004AAD;
            font-size: 9px;
            font-weight: bold;
            padding: 2.7mm 1.5mm;
            text-align: center;
        }

        .data-table th:last-child,
        .data-table td:last-child { border-left: 0; }

        .data-table td {
            background: rgba(244, 241, 235, .9);
            border-bottom: .6px solid #8BAED8;
            border-left: .7px solid #8BAED8;
            color: #004AAD;
            font-size: 8.5px;
            line-height: 1.4;
            padding: 2.3mm 1.5mm;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .data-table tbody tr:last-child td { border-bottom: 0; }

        .date-cell {
            background: #E8EEF6 !important;
            color: #004AAD;
            font-weight: bold;
        }

        .date-number {
            display: block;
            font-family: Alexandria, sans-serif;
            font-size: 7.5px;
            margin-top: 1px;
        }

        .status {

            color: #004AAD;
            font-size: 10px ;
            font-weight: bold;
            padding: 1.1mm 1.4mm;
            white-space: nowrap;
        }

        .score {
            color: #004AAD;
            font-family: Alexandria, sans-serif;
            font-size:10px;
            font-weight: bold;
            padding: 1.1mm 1.6mm;
        }

        .comment-cell {
            padding-right: 2.5mm !important;
            text-align: right !important;
            font-size:15px;
        }

        .no-record {
            background: #F4F1EB !important;
            color: #7FA5D4 !important;
            font-size: 8px !important;
            font-style: italic;
        }

        .mascot {
            bottom: -5mm;
            height: 43mm;
            position: fixed;
            right: -2mm;
            z-index: 1;
        }

        .footer-table {
            color: #004AAD;
            font-size: 7.5px;
        }

        .footer-table td {
            border-top: .6px solid #8BAED8;
            padding-top: 2mm;
        }

        .footer-page {
            direction: ltr;
            font-family: Alexandria, sans-serif;
            font-weight: bold;
            text-align: left;
        }
    </style>

</head>
<body>
    <htmlpageheader name="reportHeader">
        <div class="header-spacer"></div>
        <table class="header-table" dir="ltr">
            <tr>
                <td style="width:52%; text-align:left;">
                    <img class="brand-logo" src="{{ public_path('images/logoo.png') }}" alt="Summit Online School">
                    <span style="display:inline-block; margin-left:3mm; vertical-align:middle;">
                        <span class="brand-name">Summit</span>
                        <span class="brand-subtitle">ONLINE SCHOOL</span>
                    </span>
                </td>
                <td style="width:48%;" dir="rtl">
                    <div class="report-title">التقرير الأسبوعي</div>
                    <div class="report-subtitle">Weekly Report</div>
                </td>
            </tr>
        </table>

        <div class="top-rule"></div>
    </htmlpageheader>

    <htmlpagefooter name="reportFooter">
        <table class="footer-table" dir="rtl">
            <tr>
                <td>Summit Online School - متابعة تعليمية واضحة لكل أسرة</td>
                <td class="footer-page">PAGE {PAGENO} / {nbpg}</td>
            </tr>
        </table>
    </htmlpagefooter>

    @if($subjects->count() <= 3)
        <img class="mascot" src="{{ public_path('images/progress-character.png') }}" alt="Summit">
    @endif

    <div class="student-wrap">
        <table class="student-table" dir="rtl">
            <tr>
                <td style="width:16.66%;">
                    <div class="student-label">الطالب</div>
                    <div class="student-value">{{ $student->full_name }}</div>
                </td>
                <td style="width:16.66%;">
                    <div class="student-label">الصف الدراسي</div>
                    <div class="student-value">{{ $student->gradeLevel?->name ?? '—' }}</div>
                </td>
                <td style="width:16.66%;">
                    <div class="student-label">المسار</div>
                    <div class="student-value">{{ ($student->gradeLevel?->track?->value ?? $student->track?->value ?? $student->track) === 'arabic' ? 'عربي' : 'لغات' }}</div>
                </td>
                <td style="width:16.66%;">
                    <div class="student-label">فترة المتابعة</div>
                    <div class="student-value" dir="ltr">{{ $weekStart->format('d/m') }} - {{ $weekEnd->format('d/m') }}</div>
                </td>
                
            </tr>
        </table>
    </div>

    @php
        $days = collect(range(0, 4))->map(fn (int $offset) => $weekStart->copy()->addDays($offset));
        $arDays = [
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
            'Saturday' => 'السبت',
        ];
    @endphp

    @forelse($subjects as $subject)
        @php
            $subjectProgress = $progress->where('subject_id', $subject->id);
            $subjectName = $subject->name_ar ?? $subject->name;
            $subjectEnglish = $subject->name ?? '';
        @endphp

        <div class="subject-block">
            <table class="subject-heading" dir="rtl">
                <tr>
                    <td class="subject-name">{{ $subjectName }}</td>
                    <td class="subject-english">{{ mb_strtoupper($subjectEnglish, 'UTF-8') }}</td>
                </tr>
            </table>

            <table class="data-table" dir="rtl">
                <thead>
                    <tr>
                        <th style="width:15%;">التاريخ</th>
                        <th style="width:13%;">الحضور</th>
                        <th style="width:14%;">التفاعل</th>
                        <th style="width:11%;">الدرجة</th>
                        <th style="width:47%;">ملاحظات المعلم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($days as $day)
                        @php
                            $record = $subjectProgress->first(
                                fn ($progressRecord) => $progressRecord->date?->format('Y-m-d') === $day->toDateString()
                            );
                            $dayName = $arDays[$day->format('l')] ?? $day->format('l');
                        @endphp<
                        <tr>
                            <td class="date-cell">
                                {{ $dayName }}
                                <span class="date-number">{{ $day->format('d/m') }}</span>
                            </td>
                            @if($record)
                                <td><span class="status">{{ $record->attendance_status === 'present' ? 'حاضر' : ($record->attendance_status === 'absent' ? 'غائب' : 'متأخر') }}</span></td>
                                <td><span class="status">{{ $record->interaction_level === 'engaged' ? 'متفاعل' : 'غير متفاعل' }}</span></td>
                                <td>@if($record->score !== null)<span class="score">{{ $record->score }}/10</span>@else — @endif</td>
                                <td class="comment-cell">{{ $record->comment ?: '—' }}</td>
                            @else
                                <td colspan="4" class="no-record">لم يتم تسجيل بيانات لهذا اليوم.</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="student-wrap" style="text-align:center;">لا توجد مواد مسندة لهذا الطالب حاليًا.</div>
    @endforelse

</body>
</html>
