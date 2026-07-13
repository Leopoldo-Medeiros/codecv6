<?php

namespace App\Enums;

enum PaymentTier: string
{
    case PRACTICE = 'practice';
    case ACCELERATOR = 'accelerator';
    case BOOTCAMP = 'bootcamp';
    case MENTORSHIP = 'mentorship';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isRecurring(): bool
    {
        return in_array($this, [self::PRACTICE, self::MENTORSHIP], true);
    }
}
