<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Invoice Status Enum
 *
 * Defines all possible states an invoice can be in throughout its lifecycle.
 */
enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';
    case OVERDUE = 'overdue';

    /**
     * Get the human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Sent',
            self::PAID => 'Paid',
            self::CANCELLED => 'Cancelled',
            self::OVERDUE => 'Overdue',
        };
    }

    /**
     * Get the color associated with the status for UI display.
     */
    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SENT => 'blue',
            self::PAID => 'green',
            self::CANCELLED => 'red',
            self::OVERDUE => 'orange',
        };
    }

    /**
     * Check if the invoice can be edited in this status.
     */
    public function canBeEdited(): bool
    {
        return match ($this) {
            self::DRAFT => true,
            default => false,
        };
    }

    /**
     * Check if the invoice can be sent in this status.
     */
    public function canBeSent(): bool
    {
        return match ($this) {
            self::DRAFT => true,
            default => false,
        };
    }
}
