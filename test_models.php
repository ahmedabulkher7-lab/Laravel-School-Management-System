<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$student = \App\Models\Student::first();
echo "Student: {$student->full_name}\n";
echo "Track: {$student->track->value}\n";
echo "Subjects:\n";
foreach($student->subjects as $subject) {
    echo "- {$subject->name}\n";
}

echo "\n";

$teacher = \App\Models\Teacher::first();
echo "Teacher: {$teacher->full_name}\n";
echo "Track: {$teacher->track->value}\n";
echo "Subjects:\n";
foreach($teacher->subjects as $subject) {
    echo "- {$subject->name}\n";
}
