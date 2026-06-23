<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * ProductSeeder - Membuat 19 produk Della Frozen Mart.
 *
 * Kode produk menggunakan format PRD-XXXX.
 * Category ID merujuk ke CategorySeeder:
 * 1=Frozen Food, 2=Seafood, 3=Daging, 4=Ayam,
 * 5=Snack Frozen, 6=Saus dan Bumbu, 7=Minuman, 8=Belum Dikategorikan
 */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'kode_produk' => 'PRD-0001',
                'nama_produk' => 'Nugget Ayam Original 500g',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 120,
                'stok_minimum' => 20,
            ],
            [
                'kode_produk' => 'PRD-0002',
                'nama_produk' => 'Nugget Ayam Crispy 400g',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 45,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0003',
                'nama_produk' => 'Sosis Cocktail Mini 250g',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 80,
                'stok_minimum' => 15,
            ],
            [
                'kode_produk' => 'PRD-0004',
                'nama_produk' => 'Karage Ayam 500g',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 60,
                'stok_minimum' => 12,
            ],
            [
                'kode_produk' => 'PRD-0005',
                'nama_produk' => 'Ayam Katsu Frozen 400g',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 90,
                'stok_minimum' => 20,
            ],
            [
                'kode_produk' => 'PRD-0006',
                'nama_produk' => 'Bakso Sapi Jumbo 500g',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 55,
                'stok_minimum' => 15,
            ],
            [
                'kode_produk' => 'PRD-0007',
                'nama_produk' => 'Bakso Ikan Premium 400g',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 200,
                'stok_minimum' => 30,
            ],
            [
                'kode_produk' => 'PRD-0008',
                'nama_produk' => 'Udang Tempura 300g',
                'category_id' => 2, // Seafood
                'satuan' => 'PCS',
                'stok_saat_ini' => 35,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0009',
                'nama_produk' => 'Fiesta Chicken Nugget 450 Gr',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 150,
                'stok_minimum' => 25,
            ],
            [
                'kode_produk' => 'PRD-0010',
                'nama_produk' => 'Jamur Enoki',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 300,
                'stok_minimum' => 50,
            ],
            [
                'kode_produk' => 'PRD-0011',
                'nama_produk' => 'Kulit Lumpia Mesin Kecil',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 70,
                'stok_minimum' => 15,
            ],
            [
                'kode_produk' => 'PRD-0012',
                'nama_produk' => 'Meru Lapis Bogor',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 25,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0013',
                'nama_produk' => 'Cireng Rujak Brexcel',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PACK',
                'stok_saat_ini' => 40,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0014',
                'nama_produk' => 'Saus BBQ 500 Gr',
                'category_id' => 6, // Saus dan Bumbu
                'satuan' => 'PCS',
                'stok_saat_ini' => 30,
                'stok_minimum' => 8,
            ],
            [
                'kode_produk' => 'PRD-0015',
                'nama_produk' => 'Fiesta Kentang 1 Kg',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PACK',
                'stok_saat_ini' => 100,
                'stok_minimum' => 20,
            ],
            [
                'kode_produk' => 'PRD-0016',
                'nama_produk' => 'Mazzoni BBQ 250 Gr',
                'category_id' => 6, // Saus dan Bumbu
                'satuan' => 'PCS',
                'stok_saat_ini' => 20,
                'stok_minimum' => 5,
            ],
            [
                'kode_produk' => 'PRD-0017',
                'nama_produk' => 'Cimory Yogurt Drink Botol 240 ML',
                'category_id' => 7, // Minuman
                'satuan' => 'PCS',
                'stok_saat_ini' => 180,
                'stok_minimum' => 30,
            ],
            [
                'kode_produk' => 'PRD-0018',
                'nama_produk' => 'Yakult Ori',
                'category_id' => 7, // Minuman
                'satuan' => 'PACK',
                'stok_saat_ini' => 100,
                'stok_minimum' => 20,
            ],
            [
                'kode_produk' => 'PRD-0019',
                'nama_produk' => 'Indoprima Dimsum',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PACK',
                'stok_saat_ini' => 85,
                'stok_minimum' => 15,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
