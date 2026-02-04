<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Customer Type Enum
 *
 * Defines the type of customer (individual person or business entity).
 */
enum CustomerType: string
{
    case INDIVIDUAL = 'individual';
    case BUSINESS = 'business';

    /**
     * Get the human-readable label for the customer type.
     */
    public function label(): string
    {
        return match ($this) {
            self::INDIVIDUAL => 'Individual',
            self::BUSINESS => 'Business',
        };
    }

    /**
     * Check if TIN (Tax Identification Number) is required for this customer type.
     */
    public function requiresTin(): bool
    {
        return match ($this) {
            self::BUSINESS => true,
            self::INDIVIDUAL => false,
        };
    }

    /**
     * Check if business name is required for this customer type.
     */
    public function requiresBusinessName(): bool
    {
        return match ($this) {
            self::BUSINESS => true,
            self::INDIVIDUAL => false,
        };
    }
}
