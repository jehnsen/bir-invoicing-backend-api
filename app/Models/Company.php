<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Company Model
 *
 * @property int $id
 * @property string $name
 * @property string $business_name
 * @property string $tin
 * @property string $address
 * @property string $city
 * @property string $province
 * @property string $postal_code
 * @property string $country
 * @property string|null $phone
 * @property string $email
 * @property string|null $website
 * @property string|null $bir_registration_number
 * @property string|null $bir_permit_number
 * @property bool $vat_registered
 * @property float $vat_rate
 * @property string $invoice_prefix
 * @property int $next_invoice_number
 * @property string|null $logo_path
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'business_name',
        'tin',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'phone',
        'email',
        'website',
        'bir_registration_number',
        'bir_permit_number',
        'vat_registered',
        'vat_rate',
        'invoice_prefix',
        'next_invoice_number',
        'logo_path',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'vat_registered' => 'boolean',
            'is_active' => 'boolean',
            'vat_rate' => 'decimal:2',
            'next_invoice_number' => 'integer',
        ];
    }

    /**
     * Get all users belonging to this company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all customers belonging to this company.
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get all invoices belonging to this company.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the BIR settings for this company.
     */
    public function birSetting(): HasOne
    {
        return $this->hasOne(BirSetting::class);
    }

    /**
     * Get all activity logs for this company.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Generate the next invoice number for this company.
     *
     * Uses database locking to prevent race conditions and duplicate numbers.
     */
    public function generateNextInvoiceNumber(): string
    {
        return DB::transaction(function () {
            $company = self::lockForUpdate()->find($this->id);

            $invoiceNumber = $company->invoice_prefix . '-' .
                str_pad((string) $company->next_invoice_number, 6, '0', STR_PAD_LEFT);

            $company->increment('next_invoice_number');

            return $invoiceNumber;
        });
    }

    /**
     * Increment the next invoice number.
     */
    public function incrementInvoiceNumber(): void
    {
        $this->increment('next_invoice_number');
    }
}
