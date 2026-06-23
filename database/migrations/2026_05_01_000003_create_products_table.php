<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel produk utama dengan tracking stok dan kedaluwarsa.
     */
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->string('nama_produk');
            $table->foreignId('category_id')->constrained('kategori')->onDelete('restrict');
            $table->string('satuan')->default('PCS');
            $table->integer('stok_saat_ini')->default(0);
            $table->integer('stok_minimum')->default(10);
            $table->date('tanggal_kedaluwarsa')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
