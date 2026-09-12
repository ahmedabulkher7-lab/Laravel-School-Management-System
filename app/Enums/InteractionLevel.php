<?php

namespace App\Enums;

enum InteractionLevel: string
{
    case Engaged    = 'engaged';
    case NotEngaged = 'not_engaged';

    public function label(): string
    {
        return match ($this) {
            self::Engaged    => 'متفاعل',
            self::NotEngaged => 'غير متفاعل',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Engaged    => 'badge-blue',
            self::NotEngaged => 'badge-gray',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
