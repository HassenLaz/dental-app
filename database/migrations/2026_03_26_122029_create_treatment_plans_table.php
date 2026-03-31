<?php

// database/migrations/xxxx_create_treatment_plans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('title')->default('Plan de traitement'); // e.g. "Réhabilitation complète"
            $table->text('notes')->nullable();
            $table->decimal('total_cost', 10, 3)->default(0);      // total estimated cost
            $table->decimal('amount_paid', 10, 3)->default(0);     // computed from payments
            $table->string('payment_status')->default('pending');   // pending, partial, paid
            $table->string('status')->default('draft');             // draft, approved, in_progress, completed
            $table->date('created_date');                           // when plan was proposed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_plans');
    }
};
