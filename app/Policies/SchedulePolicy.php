<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;

class SchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(["admin", "teacher", "student"]);
    }

    public function view(User $user, Schedule $schedule): bool
    {
        if ($user->hasRole("admin")) {
            return true;
        }

        if ($user->hasRole("teacher") && $user->teacher) {
            return $schedule->teacher_id === $user->teacher->id
                || $user->teacher->gradeLevels()->whereKey($schedule->grade_level_id)->exists();
        }

        if ($user->hasRole("student") && $user->student) {
            return $schedule->grade_level_id === $user->student->grade_level_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole("admin");
    }

    public function update(User $user, Schedule $schedule): bool
    {
        return $user->hasRole("admin");
    }

    public function delete(User $user, Schedule $schedule): bool
    {
        return $user->hasRole("admin");
    }
}
