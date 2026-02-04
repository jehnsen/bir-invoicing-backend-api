<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * User Role Enum
 *
 * Defines user roles and their permissions within the system.
 */
enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case COMPANY_ADMIN = 'company_admin';
    case ACCOUNTANT = 'accountant';
    case STAFF = 'staff';

    /**
     * Get the human-readable label for the user role.
     */
    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::COMPANY_ADMIN => 'Company Administrator',
            self::ACCOUNTANT => 'Accountant',
            self::STAFF => 'Staff',
        };
    }

    /**
     * Get the list of permissions for this role.
     *
     * @return array<string>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::SUPER_ADMIN => [
                'manage_all_companies',
                'manage_users',
                'manage_invoices',
                'manage_customers',
                'manage_payments',
                'submit_to_bir',
                'view_reports',
                'manage_settings',
            ],
            self::COMPANY_ADMIN => [
                'manage_company',
                'manage_users',
                'manage_invoices',
                'manage_customers',
                'manage_payments',
                'submit_to_bir',
                'view_reports',
                'manage_settings',
            ],
            self::ACCOUNTANT => [
                'manage_invoices',
                'manage_customers',
                'manage_payments',
                'submit_to_bir',
                'view_reports',
            ],
            self::STAFF => [
                'view_invoices',
                'view_customers',
                'view_reports',
            ],
        };
    }

    /**
     * Check if this role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions(), true);
    }

    /**
     * Check if this role can manage users.
     */
    public function canManageUsers(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::COMPANY_ADMIN], true);
    }

    /**
     * Check if this role can submit to BIR.
     */
    public function canSubmitToBir(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::COMPANY_ADMIN, self::ACCOUNTANT], true);
    }
}
