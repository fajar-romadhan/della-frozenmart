<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * CategorySeeder - Membuat 8 kategori produk default.
 */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Frozen Food',
                'deskripsi' => 'Makanan beku siap saji dan bahan makanan beku',
            ],
            [
                'nama_kategori' => 'Seafood',
                'deskripsi' => 'Produk hasil laut dan olahan seafood beku',
            ],
            [
                'nama_kategori' => 'Daging',
                'deskripsi' => 'Daging sapi, kambing, dan olahan daging beku',
            ],
            [
                'nama_kategori' => 'Ayam',
                'deskripsi' => 'Daging ayam dan olahan ayam beku',
            ],
            [
                'nama_kategori' => 'Snack Frozen',
                'deskripsi' => 'Camilan dan jajanan beku',
            ],
            [
                'nama_kategori' => 'Saus dan Bumbu',
                'deskripsi' => 'Saus, bumbu, dan pelengkap masakan',
            ],
            [
                'nama_kategori' => 'Minuman',
                'deskripsi' => 'Minuman dingin dan beku',
            ],
            [
                'nama_kategori' => 'Belum Dikategorikan',
                'deskripsi' => 'Produk yang belum memiliki kategori',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
