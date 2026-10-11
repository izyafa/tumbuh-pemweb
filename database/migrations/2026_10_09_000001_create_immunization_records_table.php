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
        Schema::create('immunization_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('child_id')->constrained('children')->onDelete('cascade');
            $table->foreignId('recorded_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('vaccine_name');
            $table->string('category')->default('imunisasi_dasar'); // imunisasi_dasar, vitamin_a, obat_cacing
            $table->date('administered_date');
            $table->string('batch_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immunization_records');
    }
};
