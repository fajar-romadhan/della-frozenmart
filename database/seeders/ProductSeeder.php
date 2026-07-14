<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * ProductSeeder - 31 produk Della Frozen Mart.
 * Nama produk 100% sesuai dengan data manual pengguna.
 *
 * Category ID:
 * 1=Frozen Food, 2=Seafood, 3=Daging, 4=Ayam, 5=Snack Frozen, 6=Saus dan Bumbu, 7=Minuman
 */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // No.1
            ['kode_produk' => 'PRD-0001', 'nama_produk' => 'Nugget Ayam Original 500g',     'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.2
            ['kode_produk' => 'PRD-0002', 'nama_produk' => 'Nugget Ayam Crispy 400g',        'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.3
            ['kode_produk' => 'PRD-0003', 'nama_produk' => 'Champ Nugget Kombinasi 450GR',  'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.4
            ['kode_produk' => 'PRD-0004', 'nama_produk' => 'Chicken Nugget Stick 250g',     'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.5
            ['kode_produk' => 'PRD-0005', 'nama_produk' => 'Okey Nugget Stik 500GR',         'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.6
            ['kode_produk' => 'PRD-0006', 'nama_produk' => 'Bakso Soni',                    'category_id' => 3, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.7
            ['kode_produk' => 'PRD-0007', 'nama_produk' => 'Spicy Chicken Wings 500g',      'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.8
            ['kode_produk' => 'PRD-0008', 'nama_produk' => 'Cireng Rujak',                  'category_id' => 5, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.9
            ['kode_produk' => 'PRD-0009', 'nama_produk' => 'Sosis Ayam Besar 360g',         'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.10
            ['kode_produk' => 'PRD-0010', 'nama_produk' => 'Sosis Sapi Jumbo 500g',         'category_id' => 3, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.11
            ['kode_produk' => 'PRD-0011', 'nama_produk' => 'Okey Sosis 500GR',              'category_id' => 1, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.12
            ['kode_produk' => 'PRD-0012', 'nama_produk' => 'Jamur Enoki',                   'category_id' => 1, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.13
            ['kode_produk' => 'PRD-0013', 'nama_produk' => 'Sosis Kanzler Beef 500g',       'category_id' => 3, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.14
            ['kode_produk' => 'PRD-0014', 'nama_produk' => 'Sosis Kanzler Cheese 300g',     'category_id' => 1, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.15
            ['kode_produk' => 'PRD-0015', 'nama_produk' => 'Sosis Kanzler Cheese 500g',     'category_id' => 1, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.16
            ['kode_produk' => 'PRD-0016', 'nama_produk' => 'Bakso Sapi Jumbo 500g',         'category_id' => 3, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.17
            ['kode_produk' => 'PRD-0017', 'nama_produk' => 'Warisan Isi 25',                'category_id' => 5, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.18
            ['kode_produk' => 'PRD-0018', 'nama_produk' => 'Warisan Isi 50',                'category_id' => 5, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.19
            ['kode_produk' => 'PRD-0019', 'nama_produk' => 'Chicken Nugget Stick 500g',     'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.20
            ['kode_produk' => 'PRD-0020', 'nama_produk' => 'Onion Ring Frozen 250g',        'category_id' => 5, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.21
            ['kode_produk' => 'PRD-0021', 'nama_produk' => 'Onion Ring Frozen 500g',        'category_id' => 5, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.22
            ['kode_produk' => 'PRD-0022', 'nama_produk' => 'Meru Lapis Bogor',              'category_id' => 5, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.23
            ['kode_produk' => 'PRD-0023', 'nama_produk' => 'Richeese Nugget',                'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.24
            ['kode_produk' => 'PRD-0024', 'nama_produk' => 'Champ Sosis Sapi 375 gr',       'category_id' => 3, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.25
            ['kode_produk' => 'PRD-0025', 'nama_produk' => 'Sallam Nugget 250 gr',          'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.26
            ['kode_produk' => 'PRD-0026', 'nama_produk' => 'Salam Nugget 500GR',            'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.27
            ['kode_produk' => 'PRD-0027', 'nama_produk' => 'Sallam Bakso Sapi 500 gr',      'category_id' => 3, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.28
            ['kode_produk' => 'PRD-0028', 'nama_produk' => 'Fiesta Chicken Nugget 450GR',    'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.29
            ['kode_produk' => 'PRD-0029', 'nama_produk' => 'Fiesta Kentang 500 gr',         'category_id' => 5, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.30
            ['kode_produk' => 'PRD-0030', 'nama_produk' => 'Belfood Sosis Isi 30',          'category_id' => 1, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
            // No.31
            ['kode_produk' => 'PRD-0031', 'nama_produk' => 'Belfood Chicken Nugget 500gr',  'category_id' => 4, 'satuan' => 'PCS', 'stok_saat_ini' => 0, 'stok_minimum' => 10],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['kode_produk' => $product['kode_produk']],
                $product
            );
        }
    }
}
