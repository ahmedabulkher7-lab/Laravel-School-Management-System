<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Teacher;

class TeacherPolicy {
    public function viewAny(User $user): bool { return $user->hasRole('admin'); }
    public function view(User $user, Teacher $teacher): bool {
        return $user->hasRole('admin') || ($user->hasRole('teacher') && $user->teacher?->id === $teacher->id);
    }
    public function create(User $user): bool { return $user->hasRole('admin'); }
    public function update(User $user, Teacher $teacher): bool { return $user->hasRole('admin'); }
    public function delete(User $user, Teacher $teacher): bool { return $user->hasRole('admin'); }
}
