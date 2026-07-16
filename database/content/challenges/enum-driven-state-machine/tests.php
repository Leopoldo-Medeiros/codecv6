<?php

use PHPUnit\Framework\TestCase;

class StateMachineTest extends TestCase
{
    public function test_draft_can_transition_to_sent(): void
    {
        $invoice = new Invoice(1, InvoiceStatus::Draft);
        $next    = $invoice->transitionTo(InvoiceStatus::Sent);
        $this->assertNotNull($next);
        $this->assertSame(InvoiceStatus::Sent, $next->status);
    }

    public function test_draft_cannot_transition_to_paid(): void
    {
        $invoice = new Invoice(1, InvoiceStatus::Draft);
        $this->assertNull($invoice->transitionTo(InvoiceStatus::Paid));
    }

    public function test_sent_can_transition_to_paid_or_cancelled(): void
    {
        $invoice = new Invoice(2, InvoiceStatus::Sent);
        $this->assertNotNull($invoice->transitionTo(InvoiceStatus::Paid));
        $this->assertNotNull($invoice->transitionTo(InvoiceStatus::Cancelled));
    }

    public function test_paid_is_terminal(): void
    {
        $invoice = new Invoice(3, InvoiceStatus::Paid);
        $this->assertNull($invoice->transitionTo(InvoiceStatus::Cancelled));
    }

    public function test_transition_preserves_id(): void
    {
        $invoice = new Invoice(42, InvoiceStatus::Draft);
        $next    = $invoice->transitionTo(InvoiceStatus::Sent);
        $this->assertSame(42, $next->id);
    }
}
