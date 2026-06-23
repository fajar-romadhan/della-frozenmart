<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel hasil analisis persediaan menggunakan metode Safety Stock.
     * Menyimpan perhitungan weekday/weekend/event sales, safety stock, dan ROP.
     */
    public function up(): void
    {
        Schema::create('analisa_persediaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('produk')->onDelete('restrict');
            $table->decimal('weekday_sales', 10, 2)->default(0);
            $table->decimal('weekend_sales', 10, 2)->default(0);
            $table->decimal('event_sales', 10, 2)->default(0);
            $table->decimal('average_usage', 10, 2)->default(0);
            $table->decimal('max_sales', 10, 2)->default(0);
            $table->integer('lead_time')->default(3);
            $table->decimal('safety_stock', 10, 2)->default(0);
            $table->decimal('reorder_point', 10, 2)->default(0);
            $table->integer('stok_saat_ini')->default(0);
            $table->enum('status_stok', ['Aman', 'Warning', 'Order'])->default('Aman');
            $table->text('rekomendasi')->nullable();
            $table->date('tanggal_analisis');
            $table->foreignId('user_id')->constrained('pengguna')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisa_persediaan');
    }
};
