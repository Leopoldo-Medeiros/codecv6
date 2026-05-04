<?php

namespace App\Enums;

enum ChallengeDifficulty: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';
    case Expert = 'expert';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
