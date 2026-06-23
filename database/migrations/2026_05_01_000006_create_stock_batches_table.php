<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel batch stok untuk tracking FIFO dan kedaluwarsa per batch.
     */
    public function up(): void
    {
        Schema::create('batch_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('produk')->onDelete('restrict');
            $table->foreignId('incoming_good_id')->constrained('barang_masuk')->onDelete('restrict');
            $table->string('batch_code');
            $table->date('tanggal_masuk');
            $table->date('tanggal_kedaluwarsa')->nullable();
            $table->integer('jumlah_awal');
            $table->integer('jumlah_sisa');
            $table->string('satuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_stok');
    }
};
