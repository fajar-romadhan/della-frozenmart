<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * ProductSeeder - Membuat 96 produk Della Frozen Mart.
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
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0002',
                'nama_produk' => 'Nugget Ayam Crispy 400g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0003',
                'nama_produk' => 'Champ Nugget KombinasiI 450GR',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0004',
                'nama_produk' => 'Chicken Nugget Stick 250g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0005',
                'nama_produk' => 'Chicken Nugget Stick 500g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0006',
                'nama_produk' => 'Karage Ayam 500g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0007',
                'nama_produk' => 'Ayam Katsu Frozen 400g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0008',
                'nama_produk' => 'Chicken Wings Frozen 500g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0009',
                'nama_produk' => 'Chicken Wings Frozen 1 Kg',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0010',
                'nama_produk' => 'Spicy Chicken Wings 500g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0011',
                'nama_produk' => 'Spicy Chicken Wings 1 Kg',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0012',
                'nama_produk' => 'Chicken Popcorn 250g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0013',
                'nama_produk' => 'Chicken Popcorn 500g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0014',
                'nama_produk' => 'Chicken Popcorn 1 Kg',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0015',
                'nama_produk' => 'Sosis Ayam Besar 360g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0016',
                'nama_produk' => 'Sosis Sapi Jumbo 500g',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0017',
                'nama_produk' => 'Sosis okay Mini 250g',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0018',
                'nama_produk' => 'Sosis Bakar Ayam 500g',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0019',
                'nama_produk' => 'Sosis Kanzler Beef 500g',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0020',
                'nama_produk' => 'Sosis Kanzler Cheese 300g',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0021',
                'nama_produk' => 'Sosis Kanzler Cheese 500g',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0022',
                'nama_produk' => 'Bakso Sapi Jumbo 500g',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0023',
                'nama_produk' => 'Bakso Ikan Premium 400g',
                'category_id' => 2, // Seafood
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0024',
                'nama_produk' => 'Bakso Udang Goreng 300g',
                'category_id' => 2, // Seafood
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0025',
                'nama_produk' => 'Bakso Frozen 500 gram',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0026',
                'nama_produk' => 'Bakso Frozen 1 Kg',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0027',
                'nama_produk' => 'Bakso Mercon Frozen 500g',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0028',
                'nama_produk' => 'Bakso Mercon Frozen 1 Kg',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0029',
                'nama_produk' => 'Baso Aci Frozen 300g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0030',
                'nama_produk' => 'Baso Aci Frozen 500g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0031',
                'nama_produk' => 'Dimsum Ayam 300g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0032',
                'nama_produk' => 'Dimsum Ayam 500 gram',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0033',
                'nama_produk' => 'Siomay Frozen 300g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0034',
                'nama_produk' => 'Udang Tempura 300g',
                'category_id' => 2, // Seafood
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0035',
                'nama_produk' => 'Ikan Dori Fillet 400g',
                'category_id' => 2, // Seafood
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0036',
                'nama_produk' => 'Fish Roll Frozen 500g',
                'category_id' => 2, // Seafood
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0037',
                'nama_produk' => 'Fish Roll Frozen 1 Kg',
                'category_id' => 2, // Seafood
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0038',
                'nama_produk' => 'WARISAN ISI 25',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0039',
                'nama_produk' => 'WARISAN ISI 50',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0040',
                'nama_produk' => 'Kentang Goreng Beku 1 Kg',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0041',
                'nama_produk' => 'Kentang Goreng 500 gram',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0042',
                'nama_produk' => 'Onion Ring Frozen 250g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0043',
                'nama_produk' => 'Onion Ring Frozen 500g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0044',
                'nama_produk' => 'Onion Ring Frozen 1 Kg',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0045',
                'nama_produk' => 'Gyoza Ayam Sayur 240g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0046',
                'nama_produk' => 'Gyoza Udang 200g',
                'category_id' => 2, // Seafood
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0047',
                'nama_produk' => 'Otak-otak Bandung 200g',
                'category_id' => 2, // Seafood
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0048',
                'nama_produk' => 'Cireng Isi Ayam 250g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0049',
                'nama_produk' => 'Rujak Cireng 400g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0050',
                'nama_produk' => 'Corn Dog Mini 300g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0051',
                'nama_produk' => 'Risoles Frozen 250g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0052',
                'nama_produk' => 'AICE MIKI MIKI',
                'category_id' => 7, // Minuman
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0053',
                'nama_produk' => 'AICE MILK MELON',
                'category_id' => 7, // Minuman
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0054',
                'nama_produk' => 'AICE NANAS STIK',
                'category_id' => 7, // Minuman
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0055',
                'nama_produk' => 'AICE SEMANGKA',
                'category_id' => 7, // Minuman
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0056',
                'nama_produk' => 'AICE STRAWBERY',
                'category_id' => 7, // Minuman
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0057',
                'nama_produk' => 'AICE SUNDAY COKELAT',
                'category_id' => 7, // Minuman
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0058',
                'nama_produk' => 'AICE SUNDAY STRAWBERY',
                'category_id' => 7, // Minuman
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0059',
                'nama_produk' => 'AICE SWEET CORN',
                'category_id' => 7, // Minuman
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0060',
                'nama_produk' => 'Es Krim Family Pack 750 ml',
                'category_id' => 7, // Minuman
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0061',
                'nama_produk' => 'Es Krim Family Pack 1 Liter',
                'category_id' => 7, // Minuman
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0062',
                'nama_produk' => 'Saos Sambal 340 ml',
                'category_id' => 6, // Saus dan Bumbu
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0063',
                'nama_produk' => 'Saos Sambal 1 Kg',
                'category_id' => 6, // Saus dan Bumbu
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0064',
                'nama_produk' => 'Saos Spageti 350 gram',
                'category_id' => 6, // Saus dan Bumbu
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0065',
                'nama_produk' => 'Saos Spageti 500 gram',
                'category_id' => 6, // Saus dan Bumbu
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0066',
                'nama_produk' => 'Spageti (Pasta) 200 gram',
                'category_id' => 8, // Belum Dikategorikan
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0067',
                'nama_produk' => 'Spageti (Pasta) 500 gram',
                'category_id' => 8, // Belum Dikategorikan
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0068',
                'nama_produk' => 'Jamur Enoki 100 gram',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0069',
                'nama_produk' => 'Kulit dimsum',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0070',
                'nama_produk' => 'Donat Frozen Isi Coklat 500g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0071',
                'nama_produk' => 'Donat Frozen Isi Keju 500g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0072',
                'nama_produk' => 'Donat Frozen Mini 500g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0073',
                'nama_produk' => 'Piscok Lumer 250g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0074',
                'nama_produk' => 'Piscok Lumer 500g',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0075',
                'nama_produk' => 'Piscok Lumer 1 Kg',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0076',
                'nama_produk' => 'Cimory Yogurt Drink Botol 240 ML',
                'category_id' => 7, // Minuman
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0077',
                'nama_produk' => 'Cimory Yogurt 120G',
                'category_id' => 7, // Minuman
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0078',
                'nama_produk' => 'Mie Ayam Tanggamus',
                'category_id' => 8, // Belum Dikategorikan
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0079',
                'nama_produk' => 'Bumbu Bakso Sony',
                'category_id' => 6, // Saus dan Bumbu
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0080',
                'nama_produk' => 'Mamayo',
                'category_id' => 6, // Saus dan Bumbu
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0081',
                'nama_produk' => 'Meru Lapis Bogor',
                'category_id' => 5, // Snack Frozen
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0082',
                'nama_produk' => 'Richees Nugget',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0083',
                'nama_produk' => 'Champ Sosis Sapi 375 gr',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0084',
                'nama_produk' => 'Sallam Nugget 250 gr',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0085',
                'nama_produk' => 'Sallam Nugget 500 gr',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0086',
                'nama_produk' => 'Sallam Nugget 1 kg',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0087',
                'nama_produk' => 'Sallam Bakso Ayam 500 gr',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0088',
                'nama_produk' => 'Sallam Bakso Sapi 500 gr',
                'category_id' => 3, // Daging
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0089',
                'nama_produk' => 'Fiesta Karage 450 gr',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0090',
                'nama_produk' => 'Fiesta Kentang 500 gr',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0091',
                'nama_produk' => 'Fiesta Kentang 1 kg',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0092',
                'nama_produk' => 'Fiesta Nugget Dino 450 gr',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0093',
                'nama_produk' => 'Belfood Sosis Isi 30',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0094',
                'nama_produk' => 'Belfood Sosis 475 gr Isi 15',
                'category_id' => 1, // Frozen Food
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0095',
                'nama_produk' => 'So Eco Nugget 500 gr',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'PRD-0096',
                'nama_produk' => 'So Eco Nugget 1 kg',
                'category_id' => 4, // Ayam
                'satuan' => 'PCS',
                'stok_saat_ini' => 50,
                'stok_minimum' => 10,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
