<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories with product count.
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('nama_kategori')
            ->paginate(15);

        return view('kategori.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('kategori.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:255', 'unique:kategori,nama_kategori'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.max' => 'Nama kategori maksimal 255 karakter.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
        ]);

        Category::create($validated);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:255', 'unique:kategori,nama_kategori,' . $kategori->id],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.max' => 'Nama kategori maksimal 255 karakter.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
        ]);

        $kategori->update($validated);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $kategori)
    {
        // Prevent deleting default category
        if ($kategori->nama_kategori === 'Belum Dikategorikan') {
            return redirect()->route('kategori.index')
                ->with('error', 'Kategori "Belum Dikategorikan" tidak dapat dihapus karena merupakan kategori default.');
        }

        // Check if category has products
        if ($kategori->products()->count() > 0) {
            return redirect()->route('kategori.index')
                ->with('error', "Kategori '{$kategori->nama_kategori}' tidak dapat dihapus karena masih memiliki {$kategori->products()->count()} produk.");
        }

        $kategori->delete();

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
