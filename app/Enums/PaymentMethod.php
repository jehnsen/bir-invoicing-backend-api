<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Payment Method Enum
 *
 * Defines all available payment methods for recording payments.
 */
enum PaymentMethod: string
{
    case CASH = 'cash';
    case CHECK = 'check';
    case BANK_TRANSFER = 'bank_transfer';
    case CREDIT_CARD = 'credit_card';
    case EWALLET = 'ewallet';
    case OTHER = 'other';

    /**
     * Get the human-readable label for the payment method.
     */
    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::CHECK => 'Check',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::CREDIT_CARD => 'Credit Card',
            self::EWALLET => 'E-Wallet',
            self::OTHER => 'Other',
        };
    }

    /**
     * Check if this payment method requires a reference number.
     */
    public function requiresReference(): bool
    {
        return match ($this) {
            self::CHECK, self::BANK_TRANSFER, self::CREDIT_CARD => true,
            default => false,
        };
    }
}
