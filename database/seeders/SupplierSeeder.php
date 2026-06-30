<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

/**
 * SupplierSeeder - Membuat 18 supplier default untuk persediaan.
 */
class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
