<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Payment Status Enum
 *
 * Defines the payment state of an invoice.
 */
enum PaymentStatus: string
{
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case REFUNDED = 'refunded';

    /**
     * Get the human-readable label for the payment status.
     */
    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Unpaid',
            self::PARTIAL => 'Partially Paid',
            self::PAID => 'Paid',
            self::REFUNDED => 'Refunded',
        };
    }

    /**
     * Get the color associated with the payment status for UI display.
     */
    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'red',
            self::PARTIAL => 'yellow',
            self::PAID => 'green',
            self::REFUNDED => 'purple',
        };
    }

    /**
     * Check if additional payments can be recorded for this status.
     */
    public function canReceivePayment(): bool
    {
        return match ($this) {
            self::UNPAID, self::PARTIAL => true,
            default => false,
        };
    }
}
