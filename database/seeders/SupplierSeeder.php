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
        $suppliers = [
            [
                'nama_supplier' => 'PT Sumber Frozen',
                'kontak' => 'Budi Santoso',
                'telepon' => '0812-3456-7890',
                'email' => 'info@sumberfrozen.co.id',
                'alamat' => 'Jl. Raya Bekasi No. 123, Jakarta Timur',
                'keterangan' => 'Pemasok makanan beku utama',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'PT Lautan Sejahtera',
                'kontak' => 'Rina Wijaya',
                'telepon' => '0813-9876-5432',
                'email' => 'sales@lautansejahtera.co.id',
                'alamat' => 'Jl. H. Juanda No. 45, Bekasi',
                'keterangan' => 'Pemasok seafood dan ikan beku',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'CV Sejahtera Abadi',
                'kontak' => 'Agus Setiawan',
                'telepon' => '0812-1111-2222',
                'email' => 'sejahteraabadi.cv@gmail.com',
                'alamat' => 'Jl. Daan Mogot No. 88, Jakarta Barat',
                'keterangan' => 'Pemasok nugget dan sosis',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'PT Indo Seafood',
                'kontak' => 'Dewi Lestari',
                'telepon' => '0813-2222-3333',
                'email' => 'marketing@indoseafood.co.id',
                'alamat' => 'Jl. Muara Baru No. 10, Jakarta Utara',
                'keterangan' => 'Pemasok olahan seafood premium',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'CV Frozen Food Nusantara',
                'kontak' => 'Hendra Gunawan',
                'telepon' => '0812-4444-5555',
                'email' => 'cs@frozenfoodnusantara.co.id',
                'alamat' => 'Jl. Soekarno Hatta No. 99, Bandung',
                'keterangan' => 'Pemasok kentang goreng lokal',
                'status_aktif' => false,
            ],
            [
                'nama_supplier' => 'PT Mega Artha',
                'kontak' => 'Maya Sari',
                'telepon' => '0813-6666-7777',
                'email' => 'order@megaartha.co.id',
                'alamat' => 'Jl. Raya Serpong No. 17, Tangerang',
                'keterangan' => 'Pemasok kemasan beku dan plastik',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'UD Sukses Bersama',
                'kontak' => 'Yudi Kurniawan',
                'telepon' => '0812-8888-9999',
                'email' => 'suksesbersama.ud@gmail.com',
                'alamat' => 'Jl. A. Yani No. 200, Surabaya',
                'keterangan' => 'Pemasok bumbu dan saus frozen food',
                'status_aktif' => false,
            ],
            [
                'nama_supplier' => 'Supplier Tidak Diketahui',
                'kontak' => '-',
                'telepon' => '-',
                'email' => '-',
                'alamat' => '-',
                'keterangan' => 'Supplier default untuk import tanpa supplier',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'PT Charoen Pokphand',
                'kontak' => 'Joko Susilo',
                'telepon' => '0812-9999-8888',
                'email' => 'info@charoenpokphand.co.id',
                'alamat' => 'Jl. Mangga Dua No. 12, Jakarta Pusat',
                'keterangan' => 'Pemasok nugget dan sosis ayam',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'PT Belfoods Indonesia',
                'kontak' => 'Eko Wahyudi',
                'telepon' => '0813-7777-6666',
                'email' => 'contact@belfoods.co.id',
                'alamat' => 'Jl. Margonda Raya No. 45, Depok',
                'keterangan' => 'Pemasok frozen food berkualitas',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'PT Cold Chain Logistics',
                'kontak' => 'Susi Susanti',
                'telepon' => '0811-1234-5678',
                'email' => 'logistics@coldchain.co.id',
                'alamat' => 'Jl. Cakung Cilincing No. 88, Jakarta Utara',
                'keterangan' => 'Layanan sewa cold storage',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'CV Es Krim Nusantara',
                'kontak' => 'Ahmad Dhani',
                'telepon' => '0812-5555-4444',
                'email' => 'cs@eskrimnusantara.co.id',
                'alamat' => 'Jl. Malioboro No. 100, Yogyakarta',
                'keterangan' => 'Pemasok es krim literan',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'PT Frozen Bakery',
                'kontak' => 'Lisa Blackpink',
                'telepon' => '0813-4444-3333',
                'email' => 'sales@frozenbakery.co.id',
                'alamat' => 'Jl. Asia Afrika No. 50, Bandung',
                'keterangan' => 'Pemasok adonan roti beku',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'UD Daging Segar',
                'kontak' => 'Bambang Pamungkas',
                'telepon' => '0812-3333-2222',
                'email' => 'order@dagingsegar.co.id',
                'alamat' => 'Jl. Kertajaya No. 12, Surabaya',
                'keterangan' => 'Pemasok daging sapi beku',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'PT Sayur Segar',
                'kontak' => 'Siti Nurhaliza',
                'telepon' => '0813-2222-1111',
                'email' => 'contact@sayursegar.co.id',
                'alamat' => 'Jl. Jend. Sudirman No. 5, Bogor',
                'keterangan' => 'Pemasok sayuran beku campuran',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'CV Kentang Goreng',
                'kontak' => 'Baim Wong',
                'telepon' => '0812-6666-5555',
                'email' => 'cs@kentanggoreng.co.id',
                'alamat' => 'Jl. Gajah Mada No. 15, Semarang',
                'keterangan' => 'Pemasok kentang beku impor',
                'status_aktif' => false,
            ],
            [
                'nama_supplier' => 'UD Baso Bulat',
                'kontak' => 'Sule Sutisna',
                'telepon' => '0813-7777-8888',
                'email' => 'sales@basobulat.co.id',
                'alamat' => 'Jl. Pasteur No. 25, Bandung',
                'keterangan' => 'Pemasok baso ikan dan sapi beku',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'PT Milk & Cheese',
                'kontak' => 'Raffi Ahmad',
                'telepon' => '0812-8888-0000',
                'email' => 'info@milkcheese.co.id',
                'alamat' => 'Jl. Raden Saleh No. 30, Depok',
                'keterangan' => 'Pemasok keju dan susu beku',
                'status_aktif' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
