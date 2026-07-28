<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>التقرير الأسبوعي</title>
    <style>
        @page { margin: 30px 40px; }
        body {
            font-family: 'xbriyaz', 'sans-serif'; /* Default mPDF font for Arabic */
            direction: rtl;
            background: #ffffff;
            color: #333333;
            font-size: 14px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0C7261;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 { color: #1e293b; font-size: 24px; margin: 0; }
        .header p { color: #475569; font-size: 14px; margin: 5px 0 0 0; }
        
        .info-box {
            background: #f8fafc;
            border: 1px solid #0C7261;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-table { width: 100%; }
        .info-table td { padding: 5px; }
        .info-label { font-weight: bold; color: #475569; width: 120px; }

        .subject-title {
            background: #0C7261;
            color: #ffffff;
            padding: 8px 12px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            border-radius: 3px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background: #0C7261;
            color: #475569;
            text-align: right;
            padding: 8px;
            border: 1px solid #475569;
            font-size: 12px;
        }
        .data-table td {
            padding: 8px;
            border: 1px solid #475569;
            font-size: 12px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #475569;
            font-size: 12px;
            border-top: 1px solid #0C7261;
            padding-top: 10px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1> Summit - التقرير الأسبوعي</h1>
    <p>للفترة من {{ $weekStart->format('Y-m-d') }} إلى {{ $weekEnd->format('Y-m-d') }}</p>
</div>

<div class="info-box">
    <table class="info-table">
        <tr>
            <td class="info-label">اسم الطالب:</td>
            <td>{{ $student->full_name }}</td>
            <td class="info-label">المرحلة الدراسية:</td>
            <td>{{ $student->gradeLevel->name }}</td>
        </tr>
        <tr>
            <td class="info-label">اسم ولي الأمر:</td>
            <td>{{ $student->guardian_name }}</td>
            <td class="info-label">تاريخ التوليد:</td>
            <td dir="ltr" style="text-align:right;">{{ now()->format('Y-m-d H:i') }}</td>
        </tr>
    </table>
</div>

@if($assignments->isEmpty())
    <div style="text-align:center; padding: 40px; color: #475569; background: #f8fafc; border: 1px dashed #475569;">
        لا توجد مواد مسندة لهذا الطالب حالياً.
    </div>
@else
    @php
        $days = [];
        for ($i = 0; $i < 5; $i++) {
            $days[] = $weekStart->copy()->addDays($i);
        }
        $arDays = [
            'Sunday' => 'الأحد', 'Monday' => 'الإثنين', 'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة', 'Saturday' => 'السبت'
        ];
    @endphp

    @foreach($assignments as $assignment)
        @php 
            $subject = $assignment->subject; 
            $subjectProgress = $progress->where('subject_id', $subject->id);
        @endphp
        <div class="subject-title">
            {{ $subject->name_ar ?? $subject->name }}
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th style="color: #ffffff;">التاريخ</th>
                    <th style="color: #ffffff;">الحضور</th>
                    <th style="color: #ffffff;">التفاعل</th>
                    <th style="color: #ffffff;">الواجب</th>
                    <th style="color: #ffffff;">الدرجة</th>
                    <th style="color: #ffffff;">ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($days as $day)
                    @php
                        $rec = $subjectProgress->firstWhere('date', $day->toDateString());
                        $dayName = $arDays[$day->format('l')] ?? $day->format('l');
                    @endphp
                    <tr>
                        <td dir="rtl" style="text-align:right;">{{ $dayName }}، {{ $day->format('d/m') }}</td>
                        @if($rec)
                            <td>
                                @if($rec->attendance_status === 'present') <span style="color:green;font-weight:bold;">حاضر</span>
                                @elseif($rec->attendance_status === 'absent') <span style="color:red;font-weight:bold;">غائب</span>
                                @else <span style="font-weight:bold;">متأخر</span> @endif
                            </td>
                            <td>
                                @if($rec->interaction_level === 'engaged') <span>متفاعل</span>
                                @else <span>غير متفاعل</span> @endif
                            </td>
                            <td>{{ $rec->homework_submitted ? 'مُسلّم' : 'غير مُسلّم' }}</td>
                            <td dir="ltr" style="text-align:right;">{{ $rec->score !== null ? $rec->score . '/10' : '-' }}</td>
                            <td>{{ $rec->comment ?? '-' }}</td>
                        @else
                            <td colspan="5" style="text-align:center; color:#94a3b8; background:#f8fafc;">
                                لم يتم تسجيل بيانات لهذا اليوم
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
@endif



</body>
</html>
