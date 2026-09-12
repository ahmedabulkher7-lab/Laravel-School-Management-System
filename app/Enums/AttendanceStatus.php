<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Present = 'present';
    case Absent  = 'absent';
    case Late    = 'late';

    public function label(): string
    {
        return match ($this) {
            self::Present => 'حاضر',
            self::Absent  => 'غائب',
            self::Late    => 'متأخر',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Present => 'badge-green',
            self::Absent  => 'badge-red',
            self::Late    => 'badge-yellow',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
