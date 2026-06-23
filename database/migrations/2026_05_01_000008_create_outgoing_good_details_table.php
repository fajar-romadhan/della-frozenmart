<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel detail barang keluar - mapping ke batch stok (FIFO).
     */
    public function up(): void
    {
        Schema::create('detail_barang_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outgoing_good_id')->constrained('barang_keluar')->onDelete('cascade');
            $table->foreignId('stock_batch_id')->constrained('batch_stok')->onDelete('restrict');
            $table->integer('jumlah_diambil');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_barang_keluar');
    }
};
