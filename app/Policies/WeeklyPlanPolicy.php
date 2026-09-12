<?php

namespace App\Policies;

use App\Models\GradeLevel;
use App\Models\User;
use App\Models\WeeklyPlan;

class WeeklyPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(["admin", "teacher", "student"]);
    }

    public function view(User $user, WeeklyPlan $weeklyPlan): bool
    {
        return true;
    }

    public function create(User $user, ?GradeLevel $gradeLevel = null): bool
    {
        if ($user->hasRole("admin")) {
            return true;
        }

        if ($user->hasRole("teacher") && $user->teacher && $gradeLevel) {
            return $user->teacher->gradeLevels()->whereKey($gradeLevel->id)->exists();
        }

        return false;
    }

    public function update(User $user, WeeklyPlan $weeklyPlan): bool
    {
        if ($user->hasRole("admin")) {
            return true;
        }

        if ($user->hasRole("teacher") && $user->teacher) {
            return $weeklyPlan->teacher_id === $user->teacher->id;
        }

        return false;
    }

    public function delete(User $user, WeeklyPlan $weeklyPlan): bool
    {
        return $user->hasRole("admin");
    }
}
