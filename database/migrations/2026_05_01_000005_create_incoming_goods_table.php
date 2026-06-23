<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel barang masuk / penerimaan barang dari supplier.
     */
    public function up(): void
    {
        Schema::create('barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('produk')->onDelete('restrict');
            $table->foreignId('supplier_id')->nullable()->constrained('supplier')->onDelete('set null');
            $table->date('tanggal_masuk');
            $table->integer('jumlah');
            $table->string('satuan');
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->date('tanggal_kedaluwarsa')->nullable();
            $table->string('batch_code');
            $table->string('sumber_import')->nullable();
            $table->string('nama_file_import')->nullable();
            $table->string('id_lokasi')->nullable();
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
        Schema::dropIfExists('barang_masuk');
    }
};
