<?php
namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PenjualanImport implements ToCollection, WithHeadingRow
{
    protected $validRows = [];
    protected $errorRows = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; 

            // Fallback column names in case headers vary slightly
            $tanggal = $row['tanggal_penjualan'] ?? $row['tanggal'] ?? null;
            $namaBarang = $row['nama_produk'] ?? $row['kode_produk'] ?? $row['produk'] ?? null;
            $jumlah = $row['jumlah_terjual'] ?? $row['jumlah'] ?? $row['qty'] ?? null;

            if (empty($tanggal) && empty($namaBarang) && empty($jumlah)) {
                continue;
            }

            $errors = [];

            if (!$tanggal) {
                $errors[] = 'Tanggal kosong';
            } else {
                try {
                    // Try different formats
                    Carbon::parse($tanggal);
                } catch (\Exception $e) {
                    $errors[] = 'Format tanggal tidak valid';
                }
            }

            if (!$namaBarang || trim($namaBarang) === '') {
                $errors[] = 'Nama produk kosong';
            }

            if ($jumlah === null || $jumlah === '') {
                $errors[] = 'Jumlah kosong';
            } elseif (!is_numeric($jumlah) || $jumlah <= 0) {
                $errors[] = 'Jumlah harus angka positif';
            }

            if (count($errors) > 0) {
                $this->errorRows[] = [
                    'row_number' => $rowNumber,
                    'tanggal' => $tanggal,
                    'nama_barang' => $namaBarang,
                    'error' => implode(', ', $errors)
                ];
            } else {
                $this->validRows[] = [
                    'tanggal' => Carbon::parse($tanggal)->format('Y-m-d'),
                    'nama_barang' => trim(preg_replace('/\s+/', ' ', $namaBarang)),
                    'jumlah' => (int) $jumlah
                ];
            }
        }
    }

    public function getValidRows()
    {
        return $this->validRows;
    }

    public function getErrorRows()
    {
        return $this->errorRows;
    }
}
