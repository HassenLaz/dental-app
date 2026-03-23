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
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('tooth_number')->nullable(); // ISO Notation (11-48)
            $table->string('name'); // e.g. "Extraction", "Détartrage"
            $table->text('description')->nullable();
            $table->decimal('cost', 10, 3)->default(0); // 3 decimals for TND
            $table->decimal('amount_paid', 10, 3)->default(0);
            $table->string('payment_status')->default('pending'); // pending, partial, paid
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};
