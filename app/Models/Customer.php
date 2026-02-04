<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CustomerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Customer Model
 *
 * @property int $id
 * @property int $company_id
 * @property CustomerType $customer_type
 * @property string $name
 * @property string|null $business_name
 * @property string|null $tin
 * @property string|null $email
 * @property string|null $phone
 * @property string $address
 * @property string|null $city
 * @property string|null $province
 * @property string|null $postal_code
 * @property string $country
 * @property string|null $contact_person
 * @property string|null $notes
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'customer_type',
        'name',
        'business_name',
        'tin',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'contact_person',
        'notes',
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
            'customer_type' => CustomerType::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the company that owns this customer.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all invoices for this customer.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
