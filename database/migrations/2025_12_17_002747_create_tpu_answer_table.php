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
        Schema::create('tpu_answer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('exams_question')->constrained('tpu_questions')->cascadeOnDelete();
            $table->foreignId('exams_option')->constrained('tpu_options')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'exams_question']);
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tpu_answer');
    }
};
