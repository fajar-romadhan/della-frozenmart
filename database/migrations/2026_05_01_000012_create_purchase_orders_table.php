<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel purchase order / pemesanan barang ke supplier.
     */
    public function up(): void
    {
        Schema::create('pemesanan_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('supplier')->onDelete('restrict');
            $table->foreignId('product_id')->constrained('produk')->onDelete('restrict');
            $table->integer('jumlah_pesan');
            $table->date('tanggal_pemesanan');
            $table->enum('status_pemesanan', ['draft', 'dipesan', 'diterima', 'dibatalkan'])->default('draft');
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
        Schema::dropIfExists('pemesanan_supplier');
    }
};
