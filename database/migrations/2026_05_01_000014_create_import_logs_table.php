<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel log import file Excel (faktur pembelian & penjualan).
     */
    public function up(): void
    {
        Schema::create('log_import', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_import', ['faktur_pembelian', 'penjualan']);
            $table->string('nama_file');
            $table->integer('jumlah_baris')->default(0);
            $table->integer('jumlah_berhasil')->default(0);
            $table->integer('jumlah_gagal')->default(0);
            $table->text('catatan_error')->nullable();
            $table->foreignId('user_id')->constrained('pengguna')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_import');
    }
};
