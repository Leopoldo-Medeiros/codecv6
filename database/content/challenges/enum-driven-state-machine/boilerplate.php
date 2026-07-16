<?php

enum InvoiceStatus: string
{
    case Draft     = 'draft';
    case Sent      = 'sent';
    case Paid      = 'paid';
    case Cancelled = 'cancelled';

    /** @return list<self> */
    public function transitions(): array
    {
        // TODO: return allowed next states for each case
    }
}

class Invoice
{
    public function __construct(
        public readonly int $id,
        public readonly InvoiceStatus $status,
    ) {}

    public function transitionTo(InvoiceStatus $next): ?static
    {
        // TODO: return a new Invoice with $next status if allowed,
        // or null if the transition is forbidden
    }
}
