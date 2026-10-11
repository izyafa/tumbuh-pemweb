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
        Schema::create('measurement_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('child_id')->constrained('children')->onDelete('cascade');
            $table->foreignId('measured_by_id')->constrained('users')->onDelete('cascade');
            $table->date('measurement_date');
            $table->integer('age_in_months');
            $table->decimal('weight', 4, 2);
            $table->decimal('height', 5, 2);
            $table->decimal('head_circum', 4, 2)->nullable();
            $table->decimal('arm_circum', 4, 2)->nullable();
            $table->decimal('zscore_tb_u', 4, 2);
            $table->decimal('zscore_bb_u', 4, 2);
            $table->decimal('zscore_bb_tb', 4, 2);
            $table->enum('stunting_status', ['severely_stunted', 'stunted', 'normal', 'tall']);
            $table->boolean('is_weight_stagnant')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurement_records');
    }
};
