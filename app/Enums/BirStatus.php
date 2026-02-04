<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * BIR Status Enum
 *
 * Defines the BIR (Bureau of Internal Revenue) submission status of an invoice.
 */
enum BirStatus: string
{
    case PENDING = 'pending';
    case SUBMITTED = 'submitted';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    /**
     * Get the human-readable label for the BIR status.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Submission',
            self::SUBMITTED => 'Submitted to BIR',
            self::ACCEPTED => 'Accepted by BIR',
            self::REJECTED => 'Rejected by BIR',
        };
    }

    /**
     * Get the color associated with the BIR status for UI display.
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::SUBMITTED => 'blue',
            self::ACCEPTED => 'green',
            self::REJECTED => 'red',
        };
    }

    /**
     * Check if the invoice can be submitted to BIR in this status.
     */
    public function canBeSubmitted(): bool
    {
        return match ($this) {
            self::PENDING, self::REJECTED => true,
            default => false,
        };
    }

    /**
     * Check if this is a final status (cannot be changed).
     */
    public function isFinal(): bool
    {
        return match ($this) {
            self::ACCEPTED => true,
            default => false,
        };
    }
}
