<?php
namespace App\Policies;

use App\Models\User;
use App\Models\DailyProgress;

class DailyProgressPolicy {
    public function viewAny(User $user): bool { return $user->hasAnyRole(['admin', 'teacher']); }
    public function view(User $user, DailyProgress $progress): bool {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('teacher')) return $user->teacher?->id === $progress->teacher_id;
        if ($user->hasRole('student')) return $user->student?->id === $progress->student_id;
        return false;
    }
    public function create(User $user): bool { return $user->hasRole('teacher'); }
    public function update(User $user, DailyProgress $progress): bool {
        return $user->hasRole('admin') || ($user->hasRole('teacher') && $user->teacher?->id === $progress->teacher_id);
    }
}
