<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('tpu_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_Pertanyaan')->constrained('tpu_questions')->cascadeOnDelete();
            $table->string('label', 1);
            $table->text('opsi_tulisan');
            $table->unique(['id_Pertanyaan', 'label']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tpu_options');
    }
    

};
