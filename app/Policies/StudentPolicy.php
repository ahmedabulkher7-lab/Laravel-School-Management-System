<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Student;

class StudentPolicy {
    public function viewAny(User $user): bool { return $user->hasAnyRole(['admin', 'teacher']); }
    public function view(User $user, Student $student): bool {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('teacher')) {
            return $user->teacher && $user->teacher->gradeLevels()->whereKey($student->grade_level_id)->exists();
        }
        if ($user->hasRole('student')) {
            return $user->student?->id === $student->id;
        }
        return false;
    }
    public function create(User $user): bool { return $user->hasRole('admin'); }
    public function update(User $user, Student $student): bool { return $user->hasRole('admin'); }
    public function delete(User $user, Student $student): bool { return $user->hasRole('admin'); }
}
