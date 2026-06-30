<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        
        // Truncate tables in order of dependency
        DB::table('detail_barang_keluar')->truncate();
        DB::table('barang_keluar')->truncate();
        DB::table('batch_stok')->truncate();
        DB::table('barang_masuk')->truncate();
        
        // Reset current stock to 0 for all products
        DB::table('produk')->update(['stok_saat_ini' => 0]);
        
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Truncate operations are non-reversible, but we can define an empty down method safely.
    }
};
