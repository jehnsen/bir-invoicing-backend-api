<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BirStatus;
use App\Enums\DiscountType;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Invoice Model
 *
 * @property int $id
 * @property int $company_id
 * @property int $customer_id
 * @property string $invoice_number
 * @property \Illuminate\Support\Carbon $invoice_date
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property string|null $reference_number
 * @property InvoiceStatus $status
 * @property PaymentStatus $payment_status
 * @property BirStatus $bir_status
 * @property float $subtotal
 * @property DiscountType $discount_type
 * @property float $discount_value
 * @property float $discount_amount
 * @property float $taxable_amount
 * @property float $vat_rate
 * @property float $vat_amount
 * @property float $total_amount
 * @property string|null $notes
 * @property string|null $terms_and_conditions
 * @property string|null $payment_instructions
 * @property \Illuminate\Support\Carbon|null $bir_submitted_at
 * @property \Illuminate\Support\Carbon|null $bir_accepted_at
 * @property string|null $bir_reference_number
 * @property array|null $bir_response_data
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'customer_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'reference_number',
        'status',
        'payment_status',
        'bir_status',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'taxable_amount',
        'vat_rate',
        'vat_amount',
        'total_amount',
        'notes',
        'terms_and_conditions',
        'payment_instructions',
        'bir_submitted_at',
        'bir_accepted_at',
        'bir_reference_number',
        'bir_response_data',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'status' => InvoiceStatus::class,
            'payment_status' => PaymentStatus::class,
            'bir_status' => BirStatus::class,
            'discount_type' => DiscountType::class,
            'subtotal' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'taxable_amount' => 'decimal:2',
            'vat_rate' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'bir_response_data' => 'json',
            'bir_submitted_at' => 'datetime',
            'bir_accepted_at' => 'datetime',
        ];
    }

    /**
     * Get the company that owns this invoice.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the customer for this invoice.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who created this invoice.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this invoice.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all items for this invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get all payments for this invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all activity logs for this invoice.
     */
    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }

    /**
     * Calculate and update all invoice totals based on items.
     */
    public function calculateTotals(): void
    {
        $this->items->each(function (InvoiceItem $item) {
            $item->calculateTotals();
        });

        $this->subtotal = $this->items->sum('line_total');

        // Calculate discount amount
        $this->discount_amount = match ($this->discount_type) {
            DiscountType::PERCENTAGE => $this->subtotal * ($this->discount_value / 100),
            DiscountType::FIXED => $this->discount_value,
            default => 0,
        };

        // Calculate taxable amount (subtotal minus discount)
        $this->taxable_amount = $this->subtotal - $this->discount_amount;

        // Calculate VAT amount
        $this->vat_amount = $this->taxable_amount * ($this->vat_rate / 100);

        // Calculate total amount
        $this->total_amount = $this->taxable_amount + $this->vat_amount;
    }

    /**
     * Generate BIR-compliant JSON format for submission.
     *
     * @return array<string, mixed>
     */
    public function generateBirJson(): array
    {
        $this->loadMissing(['company', 'customer', 'items']);

        return [
            'tin' => $this->company->tin,
            'invoice_number' => $this->invoice_number,
            'invoice_date' => $this->invoice_date->format('Y-m-d'),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'customer' => [
                'name' => $this->customer->name,
                'tin' => $this->customer->tin,
                'address' => $this->customer->address,
                'city' => $this->customer->city,
                'province' => $this->customer->province,
            ],
            'items' => $this->items->map(fn (InvoiceItem $item) => [
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit' => $item->unit,
                'unit_price' => (float) $item->unit_price,
                'amount' => (float) $item->line_total,
            ])->toArray(),
            'totals' => [
                'subtotal' => (float) $this->subtotal,
                'discount_amount' => (float) $this->discount_amount,
                'taxable_amount' => (float) $this->taxable_amount,
                'vat_rate' => (float) $this->vat_rate,
                'vat_amount' => (float) $this->vat_amount,
                'total_amount' => (float) $this->total_amount,
            ],
        ];
    }

    /**
     * Check if the invoice can be edited.
     */
    public function canBeEdited(): bool
    {
        // Can only edit draft invoices
        return $this->status === InvoiceStatus::DRAFT;
    }

    /**
     * Check if the invoice can be deleted.
     */
    public function canBeDeleted(): bool
    {
        // Cannot delete if submitted to BIR or if paid
        return $this->bir_status === BirStatus::PENDING
            && $this->payment_status === PaymentStatus::UNPAID;
    }
}
