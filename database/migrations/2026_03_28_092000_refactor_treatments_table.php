<?php

// database/migrations/xxxx_refactor_treatments_table.php
// Run: php artisan make:migration refactor_treatments_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            // Link to a plan (null = standalone treatment)
            $table->foreignId('treatment_plan_id')
                  ->nullable()
                  ->after('patient_id')
                  ->constrained()
                  ->nullOnDelete();

            // Status: standalone, planned, completed, cancelled
            $table->string('status')->default('standalone')->after('date');

            // Make cost and date nullable (plan items may not have them yet)
            $table->decimal('cost', 10, 3)->nullable()->default(null)->change();
            $table->date('date')->nullable()->default(null)->change();

            // Remove payment fields — now computed from payments table
            $table->dropColumn(['amount_paid', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->dropForeign(['treatment_plan_id']);
            $table->dropColumn(['treatment_plan_id', 'status']);
            $table->decimal('cost', 10, 3)->default(0)->change();
            $table->date('date')->nullable(false)->change();
            $table->decimal('amount_paid', 10, 3)->default(0);
            $table->string('payment_status')->default('pending');
        });
    }
};