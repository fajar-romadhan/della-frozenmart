<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * ProductSeeder - Membuat 31 produk baru Della Frozen Mart sesuai nama konsisten manual Anda.
 *
 * Kode produk menggunakan format PRD-XXXX.
 * Category ID:
 * 1=Frozen Food, 2=Seafood, 3=Daging, 4=Ayam, 5=Snack Frozen, 6=Saus dan Bumbu, 7=Minuman
 */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'kode_produk' => 'PRD-0001',
                'nama_produk' => 'Nugget Ayam Original 500g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0002',
                'nama_produk' => 'Nugget Ayam Crispy 400g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0003',
                'nama_produk' => 'Champ Nugget KombinasiI 450GR',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0004',
                'nama_produk' => 'Chicken Nugget Stick 250g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0005',
                'nama_produk' => 'Okey Nugget Stik 500gr',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0006',
                'nama_produk' => 'Bakso soni',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0007',
                'nama_produk' => 'Spicy Chicken Wings 500g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0008',
                'nama_produk' => 'Cireng Rujak',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0009',
                'nama_produk' => 'Sosis Ayam Besar 360g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0010',
                'nama_produk' => 'Sosis Sapi Jumbo 500g',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0011',
                'nama_produk' => 'Okey Sosis 500GR',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0012',
                'nama_produk' => 'Jamur enoki',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0013',
                'nama_produk' => 'Sosis Kanzler Beef 500g',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0014',
                'nama_produk' => 'Sosis Kanzler Cheese 300g',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0015',
                'nama_produk' => 'Sosis Kanzler Cheese 500g',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0016',
                'nama_produk' => 'Bakso Sapi Jumbo 500g',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0017',
                'nama_produk' => 'WARISAN ISI 25',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0018',
                'nama_produk' => 'Warisan Isi 50',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0019',
                'nama_produk' => 'Kentang Goreng 500 gram',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0020',
                'nama_produk' => 'Onion Ring Frozen 250g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0021',
                'nama_produk' => 'Onion Ring Frozen 500g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0022',
                'nama_produk' => 'Meru Lapis Bogor',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0023',
                'nama_produk' => 'Richeese Nugget',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0024',
                'nama_produk' => 'Champ Sosis Sapi 375 gr',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0025',
                'nama_produk' => 'Sallam Nugget 250 gr',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0026',
                'nama_produk' => 'Sallam Nugget 500gr',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0027',
                'nama_produk' => 'Sallam Bakso Sapi 500 gr',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0028',
                'nama_produk' => 'Fiesta chicken nugget 450gr',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0029',
                'nama_produk' => 'Fiesta Kentang 500 gr',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0030',
                'nama_produk' => 'Belfood Sosis isi 30',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0031',
                'nama_produk' => 'Belfood chicken nugget 500gr',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 0,
                'stok_minimum' => 10,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
