<?php

namespace App\Enums;

enum StudyTrack: string
{
    case Arabic = 'arabic';
    case Languages = 'languages';

    public function label(): string
    {
        return match ($this) {
            self::Arabic => 'Arabic',
            self::Languages => 'english',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
