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

        Schema::create('fuzzy_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('min_value');
            $table->integer('max_value'); 
            $table->integer('crisp_value'); 
            $table->timestamps();
        });

        Schema::create('fuzzy_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type',['TPU','WWN','PRAK','ORB'])->nullable();
            $table->unsignedInteger('score_raw');  
            $table->unsignedTinyInteger('score_crisp');
            $table->unsignedBigInteger('fuzzy_rule_id');
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('id_seleksi') ->nullable()->constrained('selections')->nullOnDelete();
            $table->foreign('fuzzy_rule_id')->references('id')->on('fuzzy_rules');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuzzy_scores');
        Schema::dropIfExists('fuzzy_rules');
    }
};
