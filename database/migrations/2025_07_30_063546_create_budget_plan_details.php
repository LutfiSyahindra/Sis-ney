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
        Schema::create('budget_plan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_plan_id')->constrained('budget_plan')->onDelete('cascade');
            $table->decimal('nominal', 15, 2);
            $table->text('keterangan')->nullable();
            $table->foreignId('aset_tabungan_id')->nullable()->constrained('aset_tabungan')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_plan_details');
    }
};
