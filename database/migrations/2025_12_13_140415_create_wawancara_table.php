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
        Schema::create('wwn_questions', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('pertanyaan');
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        Schema::create('orb_questions', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('pertanyaan');
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwn_questions');
        Schema::dropIfExists('orb_questions');
    }
};
