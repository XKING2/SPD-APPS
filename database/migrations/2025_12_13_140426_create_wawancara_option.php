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
        Schema::create('wwn_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_wwn')->constrained('wwn_questions')->cascadeOnDelete();
            $table->string('label', 2); // A, B, C, D, E
            $table->text('opsi_tulisan');
            $table->unsignedTinyInteger('point'); // 1–5 (atau bebas nanti)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwn_options');
    }
};
