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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('cin')->nullable()->unique(); // Very important for Tunisia
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birthdate');
            $table->enum('gender', ['M', 'F']);
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
