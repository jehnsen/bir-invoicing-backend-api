<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();

            // Foreign key to invoice
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');

            // Item information
            $table->string('item_code', 100)->nullable();
            $table->text('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit', 50)->default('pcs');
            $table->decimal('unit_price', 15, 2);

            // Discount and calculations
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('taxable_amount', 15, 2);
            $table->decimal('vat_amount', 15, 2);
            $table->decimal('line_total', 15, 2);

            // Ordering
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            // Indexes for performance
            $table->index(['invoice_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
