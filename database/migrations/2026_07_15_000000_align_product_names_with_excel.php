<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $renames = [
            'PRD-0003' => 'Champ Nugget Kombinasi 450GR',
            'PRD-0004' => 'Chicken Nugget Stick 250g',
            'PRD-0006' => 'Bakso Soni',
            'PRD-0019' => 'Chicken Nugget Stick 500g',
            'PRD-0025' => 'Sallam Nugget 250 gr',
            'PRD-0027' => 'Sallam Bakso Sapi 500 gr',
            'PRD-0031' => 'Belfood Chicken Nugget 500gr',
        ];

        foreach ($renames as $code => $newName) {
            DB::table('produk')
                ->where('kode_produk', $code)
                ->update(['nama_produk' => $newName]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $originalNames = [
            'PRD-0003' => 'Champ Nugget KombinasiI 450GR',
            'PRD-0004' => 'Okey Nugget Stik 250GR',
            'PRD-0006' => 'Bakso soni',
            'PRD-0019' => 'Kentang Goreng 500 gram',
            'PRD-0025' => 'Salam Nugget 250GR',
            'PRD-0027' => 'Salam Bakso Sapi 500GR',
            'PRD-0031' => 'Belfood chicken nugget 500gr',
        ];

        foreach ($originalNames as $code => $oldName) {
            DB::table('produk')
                ->where('kode_produk', $code)
                ->update(['nama_produk' => $oldName]);
        }
    }
};
