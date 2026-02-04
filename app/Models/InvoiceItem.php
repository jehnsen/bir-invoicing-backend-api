<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Invoice Item Model
 *
 * @property int $id
 * @property int $invoice_id
 * @property string|null $item_code
 * @property string $description
 * @property float $quantity
 * @property string $unit
 * @property float $unit_price
 * @property float $discount_percentage
 * @property float $discount_amount
 * @property float $taxable_amount
 * @property float $vat_amount
 * @property float $line_total
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'item_code',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'taxable_amount',
        'vat_amount',
        'line_total',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'taxable_amount' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'line_total' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the invoice that owns this item.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Calculate and update all totals for this item.
     */
    public function calculateTotals(): void
    {
        // Calculate base amount (quantity * unit price)
        $baseAmount = $this->quantity * $this->unit_price;

        // Calculate discount amount
        $this->discount_amount = $baseAmount * ($this->discount_percentage / 100);

        // Calculate taxable amount (base amount minus discount)
        $this->taxable_amount = $baseAmount - $this->discount_amount;

        // Get VAT rate from invoice
        $vatRate = $this->invoice->vat_rate ?? 12.00;

        // Calculate VAT amount
        $this->vat_amount = $this->taxable_amount * ($vatRate / 100);

        // Calculate line total
        $this->line_total = $this->taxable_amount + $this->vat_amount;
    }
}
