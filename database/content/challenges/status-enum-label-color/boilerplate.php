<?php

enum OrderStatus: string
{
    case Pending   = 'pending';
    case Paid      = 'paid';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        // TODO: return human-readable label for each case
    }

    public function color(): string
    {
        // TODO: return Tailwind color name: "yellow", "green", or "red"
    }
}
