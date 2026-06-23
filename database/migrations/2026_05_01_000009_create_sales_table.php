<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel data penjualan untuk analisis Safety Stock.
     */
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('produk')->onDelete('restrict');
            $table->date('tanggal_penjualan');
            $table->integer('jumlah_terjual');
            $table->string('sumber_import')->nullable();
            $table->string('nama_file_import')->nullable();
            $table->foreignId('user_id')->constrained('pengguna')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
