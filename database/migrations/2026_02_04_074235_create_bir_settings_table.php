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
        Schema::create('bir_settings', function (Blueprint $table) {
            $table->id();

            // One setting per company
            $table->foreignId('company_id')->unique()->constrained()->onDelete('cascade');

            // BIR API configuration
            $table->string('api_endpoint')->nullable();
            $table->text('api_key')->nullable()->comment('Encrypted');
            $table->text('api_secret')->nullable()->comment('Encrypted');
            $table->string('certificate_path')->nullable();
            $table->string('private_key_path')->nullable();

            // Mode and automation settings
            $table->string('mode')->default('test')->comment('test or production');
            $table->boolean('enable_auto_submit')->default(false);
            $table->unsignedInteger('submit_delay_hours')->default(24);

            // Additional configuration
            $table->json('configuration_data')->nullable();
            $table->boolean('is_active')->default(true);

            // Testing tracking
            $table->timestamp('last_tested_at')->nullable();
            $table->text('last_test_result')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bir_settings');
    }
};
