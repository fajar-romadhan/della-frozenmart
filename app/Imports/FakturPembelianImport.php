<?php
namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class FakturPembelianImport implements ToCollection, WithStartRow
{
    protected $validRows = [];
    protected $errorRows = [];

    public function startRow(): int
    {
        return 5;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 5; // offset by 5 because of startRow
            
            // Check if row is mostly empty (skip empty rows)
            if (empty($row[0]) && empty($row[1]) && empty($row[2])) {
                continue;
            }

            $tanggal = $row[0] ?? null;
            $namaBarang = $row[1] ?? null;
            $satuan = $row[2] ?? null;
            $stokDitambahkan = $row[3] ?? null;

            $errors = [];

            // Validate Tanggal
            if (!$tanggal) {
                $errors[] = 'Tanggal kosong';
            } else {
                try {
                    if ($tanggal instanceof \DateTime) {
                        $tanggalStr = $tanggal->format('d/m/Y');
                    } elseif (is_numeric($tanggal)) {
                        // Excel serial date
                        $tanggalStr = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal))->format('d/m/Y');
                    } else {
                        $trimmed = trim($tanggal);
                        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $trimmed)) {
                            $tanggalStr = Carbon::parse($trimmed)->format('d/m/Y');
                        } else {
                            try {
                                Carbon::createFromFormat('d/m/Y', $trimmed);
                                $tanggalStr = $trimmed;
                            } catch (\Exception $ex) {
                                $tanggalStr = Carbon::parse($trimmed)->format('d/m/Y');
                            }
                        }
                    }
                    $tanggal = $tanggalStr;
                } catch (\Exception $e) {
                    $errors[] = 'Format tanggal salah (harus DD/MM/YYYY)';
                }
            }

            // Validate Nama Barang
            if (!$namaBarang || trim($namaBarang) === '') {
                $errors[] = 'Nama barang kosong';
            }

            // Validate Satuan
            if (!$satuan || trim($satuan) === '') {
                $errors[] = 'Satuan kosong';
            } else {
                // Normalize satuan
                $satuan = strtoupper(trim($satuan));
            }

            // Validate Stok
            if ($stokDitambahkan === null || $stokDitambahkan === '') {
                $errors[] = 'Stok kosong';
            } elseif (!is_numeric($stokDitambahkan) || $stokDitambahkan <= 0) {
                $errors[] = 'Stok harus berupa angka positif';
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
                    'tanggal' => $tanggal,
                    'nama_barang' => trim(preg_replace('/\s+/', ' ', $namaBarang)),
                    'satuan' => $satuan,
                    'stok_ditambahkan' => (int) $stokDitambahkan
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
