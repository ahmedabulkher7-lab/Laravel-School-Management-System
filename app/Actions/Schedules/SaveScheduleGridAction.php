<?php

namespace App\Actions\Schedules;

use App\Models\GradeLevel;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveScheduleGridAction
{
    public function execute(GradeLevel $gradeLevel, array $slots, array $cells): void
    {
        $entries = collect($cells)->filter(fn (array $cell) => !empty($cell["subject_id"]));

        // Business rule: Validate each cell before database operation
        foreach ($entries as $cell) {
            if (!$gradeLevel->subjects->contains("id", $cell["subject_id"])) {
                throw ValidationException::withMessages([
                    "cells" => "اختر مادة مرتبطة بالصف.",
                ]);
            }

            if (!empty($cell["teacher_id"])) {
                $allowed = $gradeLevel->teachers()
                    ->whereKey($cell["teacher_id"])
                    ->whereHas("subjects", fn ($query) => $query->whereKey($cell["subject_id"]))
                    ->exists();

                if (!$allowed) {
                    throw ValidationException::withMessages([
                        "cells" => "المدرس المختار غير مسند لهذه المادة أو لهذا الصف.",
                    ]);
                }
            }
        }

        DB::transaction(function () use ($gradeLevel, $slots, $entries): void {
            Schedule::where("grade_level_id", $gradeLevel->id)->delete();

            foreach ($entries as $cellKey => $cell) {
                [$day, $slotIndex] = array_map("intval", explode("_", (string) $cellKey));

                if (!array_key_exists($slotIndex, $slots) || $day < 0 || $day > 4) {
                    continue;
                }

                $slot = $slots[$slotIndex];

                Schedule::create([
                    "grade_level_id" => $gradeLevel->id,
                    "subject_id"     => $cell["subject_id"],
                    "teacher_id"     => $cell["teacher_id"],
                    "day_of_week"    => $day,
                    "start_time"     => $slot["start_time"],
                    "end_time"       => $slot["end_time"],
                ]);
            }
        });
    }
}
