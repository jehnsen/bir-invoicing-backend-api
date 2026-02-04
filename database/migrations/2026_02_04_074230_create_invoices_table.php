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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('company_id')->constrained()->onDelete('restrict');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');

            // Invoice identification and dates
            $table->string('invoice_number', 100);
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->string('reference_number', 100)->nullable();

            // Status fields
            $table->string('status')->default('draft');
            $table->string('payment_status')->default('unpaid');
            $table->string('bir_status')->default('pending');

            // Amount fields
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('discount_type')->default('none');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('taxable_amount', 15, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(12.00);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);

            // Additional information
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->text('payment_instructions')->nullable();

            // BIR submission tracking
            $table->timestamp('bir_submitted_at')->nullable();
            $table->timestamp('bir_accepted_at')->nullable();
            $table->string('bir_reference_number')->nullable();
            $table->json('bir_response_data')->nullable();

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->unique(['company_id', 'invoice_number']);
            $table->index('invoice_date');
            $table->index('due_date');
            $table->index(['status', 'payment_status', 'bir_status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
