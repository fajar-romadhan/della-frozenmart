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
            'PRD-0004' => 'Okey Nugget Stik 250GR',
            'PRD-0005' => 'Okey Nugget Stik 500GR',
            'PRD-0008' => 'Cireng Rujak',
            'PRD-0011' => 'Okey Sosis 500GR',
            'PRD-0012' => 'Jamur Enoki',
            'PRD-0017' => 'Warisan Isi 25',
            'PRD-0018' => 'Warisan Isi 50',
            'PRD-0023' => 'Richeese Nugget',
            'PRD-0025' => 'Salam Nugget 250GR',
            'PRD-0026' => 'Salam Nugget 500GR',
            'PRD-0027' => 'Salam Bakso Sapi 500GR',
            'PRD-0028' => 'Fiesta Chicken Nugget 450GR',
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
            'PRD-0004' => 'Chicken Nugget Stick 250g',
            'PRD-0005' => 'Chicken Nugget Stick 500g',
            'PRD-0008' => 'Cireng Rujak 15gr',
            'PRD-0011' => 'Sosis okay 500g',
            'PRD-0012' => 'jamur enoki',
            'PRD-0017' => 'WARISAN ISI 25',
            'PRD-0018' => 'WARISAN ISI 50',
            'PRD-0023' => 'Richees Nugget',
            'PRD-0025' => 'Sallam Nugget 250 gr',
            'PRD-0026' => 'Sallam Nugget 500 gr',
            'PRD-0027' => 'Sallam Bakso Sapi 500 gr',
            'PRD-0028' => 'Fiesta chicken nugget 450gr',
        ];

        foreach ($originalNames as $code => $oldName) {
            DB::table('produk')
                ->where('kode_produk', $code)
                ->update(['nama_produk' => $oldName]);
        }
    }
};
