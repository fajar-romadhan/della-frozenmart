<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SafetyStockService
{
    /**
     * Calculate Safety Stock and ROP for a product.
     *
     * Formula:
     * Safety Stock = (Max Daily Sales - Average Daily Sales) × Lead Time
     * ROP = (Average Daily Sales × Lead Time) + Safety Stock
     *
     * @param Product $product
     * @param int $leadTime Lead time in days (default 3)
     * @param string|null $startDate Start of analysis period (Y-m-d)
     * @param string|null $endDate End of analysis period (Y-m-d)
     * @return array Calculated values including SS, ROP, status, recommendation
     */
    public function calculate(Product $product, int $leadTime = 3, ?string $startDate = null, ?string $endDate = null): array
    {
        // Determine end date: latest sales record date, or today if no sales exist
        if ($endDate) {
            $end = Carbon::parse($endDate);
        } else {
            $latestSale = Sale::where('product_id', $product->id)->max('tanggal_penjualan');
            $end = $latestSale ? Carbon::parse($latestSale) : Carbon::today();
        }

        // Determine start date: earliest sales record date, or 30 days prior to end date if no sales exist
        if ($startDate) {
            $start = Carbon::parse($startDate);
        } else {
            $earliestSale = Sale::where('product_id', $product->id)->min('tanggal_penjualan');
            if ($earliestSale) {
                $start = Carbon::parse($earliestSale);
                // Safeguard: if start and end are the same day, set start to 30 days ago to avoid division by zero
                if ($start->equalTo($end)) {
                    $start = $end->copy()->subDays(30);
                }
            } else {
                $start = $end->copy()->subDays(30);
            }
        }

        // Get sales data grouped by date
        $salesData = Sale::where('product_id', $product->id)
            ->whereBetween('tanggal_penjualan', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->selectRaw('tanggal_penjualan, SUM(jumlah_terjual) as total_terjual')
            ->groupBy('tanggal_penjualan')
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->tanggal_penjualan)->format('Y-m-d');
            });

        // Generate all dates in the period
        $period = CarbonPeriod::create($start, $end);
        $totalDays = 0;
        $weekdayCount = 0;
        $weekendCount = 0;
        $weekdaySalesTotal = 0;
        $weekendSalesTotal = 0;
        $totalSales = 0;
        $maxDailySales = 0;
        $dailySalesArray = [];

        foreach ($period as $date) {
            $totalDays++;
            $dateKey = $date->format('Y-m-d');
            $dailySales = isset($salesData[$dateKey]) ? (float) $salesData[$dateKey]->total_terjual : 0;
            $dailySalesArray[] = $dailySales;

            if ($dailySales > $maxDailySales) {
                $maxDailySales = $dailySales;
            }

            $totalSales += $dailySales;

            if ($date->isWeekday()) {
                $weekdayCount++;
                $weekdaySalesTotal += $dailySales;
            } else {
                $weekendCount++;
                $weekendSalesTotal += $dailySales;
            }
        }

        // Calculate averages
        $weekdaySales = $weekdayCount > 0 ? round($weekdaySalesTotal / $weekdayCount, 2) : 0;
        $weekendSales = $weekendCount > 0 ? round($weekendSalesTotal / $weekendCount, 2) : 0;
        $averageUsage = $totalDays > 0 ? round($totalSales / $totalDays, 2) : 0;

        // Safety Stock = (Max Sales - Average Usage) × Lead Time
        $safetyStock = round(($maxDailySales - $averageUsage) * $leadTime, 2);
        if ($safetyStock < 0) {
            $safetyStock = 0;
        }

        // ROP = (Average Usage × Lead Time) + Safety Stock
        $reorderPoint = round(($averageUsage * $leadTime) + $safetyStock, 2);

        // Current stock
        $stokSaatIni = $product->stok_saat_ini;

        // Determine status (Aman, Warning, Order)
        $ropInt = (int) round($reorderPoint);
        if ($stokSaatIni < $ropInt) {
            $statusStok = 'Order';
        } elseif ($stokSaatIni == $ropInt) {
            $statusStok = 'Warning';
        } else {
            $statusStok = 'Aman';
        }

        // Generate recommendation in Indonesian
        $rekomendasi = $this->generateRekomendasi($statusStok, $product, $safetyStock, $reorderPoint, $averageUsage, $leadTime);

        $result = [
            'product_id' => $product->id,
            'weekday_sales' => $weekdaySales,
            'weekend_sales' => $weekendSales,
            'event_sales' => 0,
            'average_usage' => $averageUsage,
            'max_sales' => $maxDailySales,
            'lead_time' => $leadTime,
            'safety_stock' => $safetyStock,
            'reorder_point' => $reorderPoint,
            'stok_saat_ini' => $stokSaatIni,
            'status_stok' => $statusStok,
            'rekomendasi' => $rekomendasi,
            'tanggal_analisis' => $end->format('Y-m-d'),
            'user_id' => auth()->id() ?? 1,
        ];

        \App\Models\InventoryAnalysis::create($result);

        // Trigger notification if status is Warning or Order, or mark warning as read if Aman
        try {
            if ($statusStok === 'Warning' || $statusStok === 'Order') {
                app(\App\Services\NotificationService::class)->createStockWarning($product, $statusStok);
            } elseif ($statusStok === 'Aman') {
                \App\Models\Notification::where('product_id', $product->id)
                    ->whereIn('judul', ['Stok Harus Segera Dipesan', 'Peringatan Stok Menipis'])
                    ->where('status_baca', false)
                    ->update(['status_baca' => true]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update stock notifications: ' . $e->getMessage());
        }

        return array_merge($result, [
            'total_sales' => $totalSales,
            'total_days' => $totalDays,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
        ]);
    }

    /**
     * Generate recommendation text in Bahasa Indonesia.
     */
    private function generateRekomendasi(
        string $status,
        Product $product,
        float $safetyStock,
        float $reorderPoint,
        float $averageUsage,
        int $leadTime
    ): string {
        switch ($status) {
            case 'Order':
                $jumlahPesan = ceil($reorderPoint + $safetyStock - $product->stok_saat_ini + ($averageUsage * $leadTime));
                return "SEGERA PESAN! Stok produk '{$product->nama_produk}' sudah mencapai/di bawah titik pemesanan ulang (ROP: " .
                    number_format($reorderPoint, 0, ',', '.') . "). " .
                    "Stok saat ini: {$product->stok_saat_ini} {$product->satuan}. " .
                    "Disarankan untuk memesan minimal " . number_format($jumlahPesan, 0, ',', '.') . " {$product->satuan} " .
                    "untuk memenuhi kebutuhan selama lead time ({$leadTime} hari).";

            case 'Warning':
                return "PERHATIAN! Stok produk '{$product->nama_produk}' mendekati titik pemesanan ulang. " .
                    "Stok saat ini: {$product->stok_saat_ini} {$product->satuan}, " .
                    "ROP: " . number_format($reorderPoint, 0, ',', '.') . ", " .
                    "Safety Stock: " . number_format($safetyStock, 0, ',', '.') . ". " .
                    "Segera siapkan pemesanan ke supplier.";

            case 'Aman':
            default:
                return "Stok produk '{$product->nama_produk}' dalam kondisi aman. " .
                    "Stok saat ini: {$product->stok_saat_ini} {$product->satuan}, " .
                    "ROP: " . number_format($reorderPoint, 0, ',', '.') . ", " .
                    "Safety Stock: " . number_format($safetyStock, 0, ',', '.') . ". " .
                    "Rata-rata penjualan harian: " . number_format($averageUsage, 2, ',', '.') . " {$product->satuan}/hari.";
        }
    }
}
