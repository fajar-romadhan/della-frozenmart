<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\User;

class NotificationService
{
    /**
     * Create stock warning notification for admin and manager users.
     */
    public function createStockWarning(Product $product, string $status): void
    {
        $users = User::whereIn('role', ['admin', 'manager'])
            ->where('status_aktif', true)
            ->get();

        $judul = $status === 'Order'
            ? 'Stok Harus Segera Dipesan'
            : 'Peringatan Stok Menipis';

        $pesan = $status === 'Order'
            ? "Stok produk '{$product->nama_produk}' ({$product->kode_produk}) sudah mencapai batas pemesanan ulang. " .
              "Stok saat ini: {$product->stok_saat_ini} {$product->satuan}. Segera lakukan pemesanan ke supplier."
            : "Stok produk '{$product->nama_produk}' ({$product->kode_produk}) mendekati batas minimum. " .
              "Stok saat ini: {$product->stok_saat_ini} {$product->satuan}. Harap persiapkan pemesanan.";

        foreach ($users as $user) {
            // Check if there is already an unread notification of the same title for this product
            $exists = Notification::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->where('judul', $judul)
                ->where('status_baca', false)
                ->exists();

            if (!$exists) {
                Notification::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'judul' => $judul,
                    'pesan' => $pesan,
                ]);
            }
        }
    }

    /**
     * Create expiry warning notification when a batch is near expiry (within 7 days).
     */
    public function createExpiryWarning(Product $product, StockBatch $batch): void
    {
        $users = User::whereIn('role', ['admin', 'manager'])
            ->where('status_aktif', true)
            ->get();

        $tanggalExpiry = $batch->tanggal_kedaluwarsa->format('d/m/Y');
        $sisaHari = now()->diffInDays($batch->tanggal_kedaluwarsa, false);

        $judul = 'Peringatan Kedaluwarsa Produk';
        $pesan = "Batch '{$batch->batch_code}' produk '{$product->nama_produk}' ({$product->kode_produk}) " .
                 "akan kedaluwarsa pada {$tanggalExpiry} ({$sisaHari} hari lagi). " .
                 "Sisa stok batch: {$batch->jumlah_sisa} {$batch->satuan}. " .
                 "Segera lakukan tindakan (penjualan prioritas atau penarikan).";

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'judul' => $judul,
                'pesan' => $pesan,
            ]);
        }
    }

    /**
     * Create notification after successful import.
     */
    public function createImportSuccess(string $jenis, string $namaFile, int $jumlahBerhasil, int $userId): void
    {
        $jenisLabel = $jenis === 'faktur_pembelian' ? 'Faktur Pembelian' : 'Data Penjualan';

        $judul = "Import {$jenisLabel} Berhasil";
        $pesan = "File '{$namaFile}' berhasil diimport. " .
                 "Total data berhasil diproses: {$jumlahBerhasil} baris. " .
                 "Jenis import: {$jenisLabel}.";

        // Notify the importing user
        Notification::create([
            'user_id' => $userId,
            'product_id' => null,
            'judul' => $judul,
            'pesan' => $pesan,
        ]);

        // Also notify admins if the importer is not admin
        $user = User::find($userId);
        if ($user && $user->role !== 'admin') {
            $admins = User::where('role', 'admin')
                ->where('status_aktif', true)
                ->get();

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'product_id' => null,
                    'judul' => $judul,
                    'pesan' => $pesan . " Diimport oleh: {$user->name}.",
                ]);
            }
        }
    }

    /**
     * Create notification when a batch is fully depleted.
     */
    public function createBatchEmpty(Product $product, StockBatch $batch): void
    {
        $users = User::whereIn('role', ['admin', 'manager'])
            ->where('status_aktif', true)
            ->get();

        $judul = 'Batch Stok Habis';
        $pesan = "Batch '{$batch->batch_code}' produk '{$product->nama_produk}' ({$product->kode_produk}) " .
                 "telah habis digunakan. Jumlah awal batch: {$batch->jumlah_awal} {$batch->satuan}. " .
                 "Sisa stok produk keseluruhan: {$product->stok_saat_ini} {$product->satuan}.";

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'judul' => $judul,
                'pesan' => $pesan,
            ]);
        }
    }
}
