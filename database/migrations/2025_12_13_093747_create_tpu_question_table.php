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

        Schema::create('selections', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('tahun');
            $table->foreignId('id_kecamatans') ->nullable()->constrained('kecamatans')->nullOnDelete();
            $table->foreignId('id_desas') ->nullable()->constrained('desas')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['tpu', 'wwn']);
            $table->integer('duration')->nullable();
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->string('enrollment_key', 32)->nullable()->unique();
            $table->timestamp('key_generated_at')->nullable();
            $table->timestamp('key_expired_at')->nullable();
            $table->unsignedInteger('key_used_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users') ->nullOnDelete();
            $table->foreignId('id_desas')->nullable()->constrained('desas')->nullOnDelete();
            $table->foreignId('id_seleksi') ->nullable() ->constrained('selections')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('tpu_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_exam') ->nullable()->constrained('exams')->cascadeOnDelete();
            $table->string('subject');
            $table->string('code_pertanyaan')->unique();
            $table->text('pertanyaan');
            $table->string('image_name')->nullable();
            $table->string('jawaban_benar', 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam');
        Schema::dropIfExists('tpu_questions');
    }
};
