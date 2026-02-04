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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // Core company information
            $table->string('name');
            $table->string('business_name');
            $table->string('tin', 50)->unique()->comment('Tax Identification Number');
            $table->text('address');
            $table->string('city', 100);
            $table->string('province', 100);
            $table->string('postal_code', 20);
            $table->string('country', 2)->default('PH');
            $table->string('phone', 50)->nullable();
            $table->string('email');
            $table->string('website')->nullable();

            // BIR (Bureau of Internal Revenue) information
            $table->string('bir_registration_number', 100)->nullable();
            $table->string('bir_permit_number', 100)->nullable();
            $table->boolean('vat_registered')->default(true);
            $table->decimal('vat_rate', 5, 2)->default(12.00)->comment('VAT rate percentage');

            // Invoice configuration
            $table->string('invoice_prefix', 20)->default('INV');
            $table->unsignedInteger('next_invoice_number')->default(1);

            // Additional fields
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['name', 'email', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
