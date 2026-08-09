<?php
$files = [
    'app/Http/Controllers/Admin/DashboardController.php',
    'app/Http/Controllers/Admin/ReportController.php',
    'app/Http/Controllers/Admin/ScheduleController.php',
    'app/Http/Controllers/Student/DashboardController.php',
    'app/Http/Controllers/Student/PerformanceController.php',
    'app/Http/Controllers/Student/ScheduleController.php',
    'app/Http/Controllers/Teacher/ProgressController.php',
    'app/Livewire/Teacher/DailyProgressLog.php',
    'resources/views/admin/dashboard.blade.php',
    'resources/views/admin/progress/index.blade.php',
    'resources/views/admin/schedules/index.blade.php',
    'resources/views/admin/students/show.blade.php',
    'resources/views/admin/teachers/index.blade.php',
    'resources/views/pdf/weekly-report.blade.php',
    'resources/views/student/dashboard.blade.php',
    'resources/views/student/performance.blade.php',
    'resources/views/student/schedule.blade.php',
    'resources/views/teacher/students/index.blade.php'
];

foreach($files as $file) {
    if(file_exists($file)) {
        $content = file_get_contents($file);
        
        // Fix ScheduleController
        if(strpos($file, 'ScheduleController.php') !== false) {
            $content = str_replace("Teacher::with('subject')", "Teacher::with('subjects')", $content);
        }

        // Fix admin teachers index view
        if(strpos($file, 'teachers/index.blade.php') !== false) {
            $content = str_replace('@if($teacher->subject)', '@if($teacher->subjects->isNotEmpty())', $content);
            $content = str_replace('{{ $teacher->subject->color }}', '{{ $teacher->subjects->first()->color ?? \'#ccc\' }}', $content);
            $content = str_replace('{{ $teacher->subject->name_ar ?? $teacher->subject->name }}', '{{ $teacher->subjects->pluck(\'name_ar\')->join(\', \') }}', $content);
        }

        // General fixes for views using $teacher->subject
        $content = str_replace('{{ $teacher->subject->name_ar ?? $teacher->subject->name ?? \'\' }}', '{{ $teacher->subjects->pluck(\'name_ar\')->join(\', \') }}', $content);
        $content = str_replace('{{ $teacher->subject->name_ar ?? $teacher->subject->name }}', '{{ $teacher->subjects->pluck(\'name_ar\')->join(\', \') }}', $content);

        // For Livewire component: DailyProgressLog
        if(strpos($file, 'DailyProgressLog.php') !== false) {
            $content = preg_replace('/\$teacher\?->subject_id/', '$teacher?->subjects->first()?->id', $content);
            $content = preg_replace('/\$teacher->subject_id/', '$teacher->subjects->first()?->id', $content);
        }

        file_put_contents($file, $content);
    }
}
echo "Done";
