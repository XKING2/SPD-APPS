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
        Schema::create('wwn_answer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('wawancara_question')->constrained('wwn_questions')->cascadeOnDelete();
            $table->foreignId('wawancara_option')->constrained('wwn_options')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'wawancara_question']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwn_answer');
    }
};
