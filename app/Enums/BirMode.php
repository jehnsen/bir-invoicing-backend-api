<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * BIR Mode Enum
 *
 * Defines the operational mode for BIR API integration (test or production).
 */
enum BirMode: string
{
    case TEST = 'test';
    case PRODUCTION = 'production';

    /**
     * Get the human-readable label for the BIR mode.
     */
    public function label(): string
    {
        return match ($this) {
            self::TEST => 'Test Mode',
            self::PRODUCTION => 'Production Mode',
        };
    }

    /**
     * Check if this is production mode.
     */
    public function isProduction(): bool
    {
        return $this === self::PRODUCTION;
    }

    /**
     * Check if this is test mode.
     */
    public function isTest(): bool
    {
        return $this === self::TEST;
    }

    /**
     * Get the appropriate API endpoint suffix based on mode.
     */
    public function apiSuffix(): string
    {
        return match ($this) {
            self::TEST => '/test',
            self::PRODUCTION => '/api',
        };
    }
}
