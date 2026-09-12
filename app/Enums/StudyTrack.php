<?php

namespace App\Enums;

enum StudyTrack: string
{
    case Arabic = 'arabic';
    case Languages = 'languages';
    case Both = 'both';

    public function label(): string
    {
        return match ($this) {
            self::Arabic => 'عربي',
            self::Languages => 'لغات',
            self::Both => 'عربي ولغات',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

}
