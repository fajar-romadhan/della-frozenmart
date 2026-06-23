<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel barang keluar dengan jenis: penjualan, rusak, kedaluwarsa, penyesuaian.
     */
    public function up(): void
    {
        Schema::create('barang_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('produk')->onDelete('restrict');
            $table->date('tanggal_keluar');
            $table->integer('jumlah');
            $table->enum('jenis_keluar', ['penjualan', 'rusak', 'kedaluwarsa', 'penyesuaian']);
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained('pengguna')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluar');
    }
};
