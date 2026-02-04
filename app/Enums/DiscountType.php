<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Discount Type Enum
 *
 * Defines the type of discount applied to an invoice.
 */
enum DiscountType: string
{
    case NONE = 'none';
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    /**
     * Get the human-readable label for the discount type.
     */
    public function label(): string
    {
        return match ($this) {
            self::NONE => 'No Discount',
            self::PERCENTAGE => 'Percentage',
            self::FIXED => 'Fixed Amount',
        };
    }

    /**
     * Check if discount value should be validated as percentage (0-100).
     */
    public function isPercentage(): bool
    {
        return $this === self::PERCENTAGE;
    }

    /**
     * Check if discount is applied.
     */
    public function hasDiscount(): bool
    {
        return $this !== self::NONE;
    }
}
