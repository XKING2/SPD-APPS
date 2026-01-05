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
        Schema::table('tpu_questions', function (Blueprint $table) {
            $table->foreignId('correct_option_id')
                ->nullable()
                ->after('image_name')
                ->constrained('tpu_options')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tpu_questions', function (Blueprint $table) {
            //
        });
    }
};
