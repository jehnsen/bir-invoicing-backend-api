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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Foreign key to company
            $table->foreignId('company_id')->constrained()->onDelete('cascade');

            // Customer type and basic information
            $table->string('customer_type')->default('individual')->comment('individual or business');
            $table->string('name');
            $table->string('business_name')->nullable();
            $table->string('tin', 50)->nullable()->comment('Tax Identification Number');

            // Contact information
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();

            // Address information
            $table->text('address');
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 2)->default('PH');

            // Additional fields
            $table->string('contact_person')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['company_id', 'name']);
            $table->index(['email', 'tin']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
