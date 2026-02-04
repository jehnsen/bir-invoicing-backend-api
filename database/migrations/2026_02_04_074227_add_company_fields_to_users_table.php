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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->string('role')->default('staff')->after('company_id');
            $table->boolean('is_active')->default(true)->after('remember_token');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->json('settings')->nullable()->after('last_login_at');

            // Index for performance
            $table->index(['company_id', 'role', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'role', 'is_active']);
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'role', 'is_active', 'last_login_at', 'settings']);
        });
    }
};
