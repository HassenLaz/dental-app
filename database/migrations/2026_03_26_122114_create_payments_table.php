<?php

// database/migrations/xxxx_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            // Polymorphic: links to either Treatment or TreatmentPlan
            $table->morphs('payable');                    // adds payable_id + payable_type
            $table->decimal('amount', 10, 3);
            $table->string('payment_method')->default('espèces'); // espèces, chèque, virement, carte
            $table->date('paid_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
