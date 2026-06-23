<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder - Menjalankan semua seeder secara berurutan.
 *
 * Urutan penting karena ada foreign key dependencies antar tabel.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            IncomingGoodSeeder::class,
            StockBatchSeeder::class,
            SaleSeeder::class,
            InventoryAnalysisSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
