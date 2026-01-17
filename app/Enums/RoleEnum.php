<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case CLIENT = 'client';
    case CONSULTANT = 'consultant';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromId(int $id): ?self
    {
        return match ($id) {
            1 => self::ADMIN,
            2 => self::CLIENT,
            3 => self::CONSULTANT,
            default => null,
        };
    }
}
