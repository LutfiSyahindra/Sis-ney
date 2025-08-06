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
        Schema::create('transaksi_keuangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('kategori_id')->nullable(); // Pisahkan dulu
            $table->unsignedBigInteger('aset_tabungan_id')->nullable(); // Pisahkan juga
            $table->decimal('nominal', 15, 2);
            $table->enum('tipe', ['pemasukan', 'pengeluaran']);
            $table->date('tanggal_transaksi');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Baru tambahkan foreign key-nya setelah nullable
            $table->foreign('kategori_id')->references('id')->on('kategori_transaksi')->onDelete('set null');
            $table->foreign('aset_tabungan_id')->references('id')->on('aset_tabungan')->onDelete('set null');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_keuangan');
    }
};
