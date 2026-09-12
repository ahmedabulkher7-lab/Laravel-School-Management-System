<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الخطة الأسبوعية - {{ $gradeLevel->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            background-color: #F4F1EB;
            margin: 48mm 12mm 55mm;
            header: html_planHeader;
            footer: html_planFooter;
        }

        * { box-sizing: border-box; }

        body {
            background: #F4F1EB;
            color: #470890;
            direction: rtl;
            font-family: Alexandria, sans-serif;
            font-size: 10px;
            line-height: 1.45;
        }

        .header-table,
        .meta-table,
        .plan-table,
        .footer-table {
            border-collapse: collapse;
            width: 100%;
        }

        .header-table { margin-bottom: 3mm; }
        .header-table td { vertical-align: middle; }
        .header-spacer { height: 8mm; }

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

        .meta-wrap {
            border: 1.2px solid #004AAD;
            border-radius: 4mm;
            margin-bottom: 8mm;
            overflow: hidden;
            padding: 4mm 2mm;
        }

        .meta-table td {
            border-left: .7px solid #004AAD;
            padding: 1mm 3mm;
            text-align: center;
            vertical-align: middle;
        }

        .meta-table td:last-child { border-left: 0; }

        .meta-label {
            color: #004AAD;
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 2mm;
        }

        .meta-value {
            color: #004AAD;
            font-family: Alexandria, sans-serif;
            font-size: 11px;
            font-weight: bold;
            line-height: 1.2;
        }

        .plan-table {
            border: 1.1px solid #470890;
            table-layout: fixed;
        }

        .plan-table thead { display: table-header-group; }

        .plan-table th {
            background: #470890;
            border-left: .7px solid #470890;
            border-bottom: .8px solid #470890;
            color: #F4F1EB;
            font-size: 9px;
            font-weight: bold;
            padding: 2.7mm 1.5mm;
            text-align: center;
        }

        .plan-table th:last-child,
        .plan-table td:last-child { border-left: 0; }

        .plan-table td {
            background: rgba(244, 241, 235, .9);
            border-bottom: .7px solid #470890;
            border-left: .7px solid #470890;
            color: #004AAD;
            font-size: 8.5px;
            line-height: 1.4;
            padding: 2.3mm 1.5mm;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .plan-table tbody tr:last-child td { border-bottom: 0; }

        .plan-table td.subject-cell {
            background: #E8EEF6 !important;
            color: #470890 !important;
            font-size: 9px;
            font-weight: bold;
            text-align: center !important;
            vertical-align: middle;
        }

        .subject-english {
            color: #E8EEF6;
            direction: ltr;
            display: block;
            font-family: Alexandria, sans-serif;
            font-size: 7px;
            font-weight: normal;
            letter-spacing: .7px;
            line-height: 1.35;
            margin-top: 1.2mm;
            text-align: left;
        }

        .teacher-name {
            border-bottom: .5px solid #470890;
            color: #004AAD;
            display: block;
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 1.4mm;
            padding-bottom: 1mm;
        }

        .plan-separator {
            border-top: .5px dashed #470890;
            margin: 2mm 0;
        }

        .empty-cell {
            color: #7FA5D4;
            font-family: Alexandria, sans-serif;
            font-size: 12px;
            padding-top: 0;
            text-align: center;
        }

        .approval-note {
            border-right: 2px solid #004AAD;
            color: #004AAD;
            font-size: 8.5px;
            line-height: 1.7;
            margin-top: 2mm;
            padding: 2mm 3mm;
        }

        .footer-mascot {
            height: 38mm;
            padding-right: 3mm;
            text-align: right;
        }

        .footer-mascot img {
            display: inline;
            height: 70mm;
            width: auto;
        }

        .footer-table {
            color: #004AAD;
            font-size: 7.5px;
        }

        .footer-table td {
            border-top: .6px solid #470890;
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
    <htmlpageheader name="planHeader">
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
                    <div class="report-title">الخطة الأسبوعية</div>
                    <div class="report-subtitle">Weekly Plan</div>
                </td>
            </tr>
        </table>

        <div class="top-rule"></div>
    </htmlpageheader>

    <htmlpagefooter name="planFooter">
        <div class="footer-mascot">
            <img src="{{ public_path('images/ChatGPT Image Aug 9, 2026, 07_07_49 PM.png') }}" alt="Summit mascot">
        </div>
        <table class="footer-table" dir="rtl">
            <tr>
                <td>Summit Online School - خطة تعليمية واضحة ومتكاملة</td>
                <td class="footer-page">PAGE {PAGENO} / {nbpg}</td>
            </tr>
        </table>
    </htmlpagefooter>

    <div class="meta-wrap">
        <table class="meta-table" dir="rtl">
            <tr>
                <td style="width:25%;">
                    <div class="meta-label">الصف الدراسي</div>
                    <div class="meta-value">{{ $gradeLevel->name }}</div>
                </td>
                <td style="width:25%;">
                    <div class="meta-label">المسار</div>
                    <div class="meta-value">{{ ($gradeLevel->track->value ?? $gradeLevel->track) === 'arabic' ? 'عربي' : 'لغات' }}</div>
                </td>
                <td style="width:25%;">
                    <div class="meta-label">الفترة الدراسية</div>
                    <div class="meta-value">{{ $weekStart->format('d/m/Y') }} - {{ $weekEnd->format('d/m/Y') }}</div>
                </td>

            </tr>
        </table>
    </div>

    <table class="plan-table" dir="rtl">
        <thead>
            <tr>
                <th style="width:18%;">المادة</th>
                <th style="width:27.33%;">عمل الفصل</th>
                <th style="width:27.33%;">الواجب المنزلي</th>
                <th style="width:27.34%;">أنشطة وألعاب أونلاين</th>
            </tr>
        </thead>
        <tbody>
    @foreach($summary['rows'] as $row)
        @php
            $values = ['class_work' => [], 'homework' => [], 'online_games' => []];

            foreach ($row['plans'] as $plan) {
                $teacherName = $plan->teacher?->full_name;

                foreach (array_keys($values) as $field) {
                    if ($plan->{$field}) {
                        $values[$field][] = ['teacher' => $teacherName, 'text' => $plan->{$field}];
                    }
                }
            }

            $subjectName = $row['subject']->name_ar ?? $row['subject']->name;
            $subjectEnglish = $row['subject']->name ?? '';
        @endphp

        <tr>
            <td class="subject-cell">
                {{ $subjectName }}
          
            </td>
            @foreach(['class_work', 'homework', 'online_games'] as $field)
                <td>
                    @forelse($values[$field] as $value)

                        <div>{{ $value['text'] }}</div>
                        @if(!$loop->last)<div class="plan-separator"></div>@endif
                    @empty
                        <div class="empty-cell">-</div>
                    @endforelse
                </td>
            @endforeach
        </tr>
    @endforeach
        </tbody>
    </table>




      

</body>
</html>
