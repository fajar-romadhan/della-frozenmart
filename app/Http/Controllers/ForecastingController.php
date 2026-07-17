<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\IncomingGood;
use App\Models\OutgoingGood;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ForecastingController extends Controller
{
    public function index()
    {
        $criticalIds = [11, 28, 12, 22, 5, 8, 26, 18, 30, 23];
        $criticalNames = [
            'okey sosis 500gr', 'fiesta chicken nugget 450gr', 'jamur enoki', 'meru lapis bogor', 'okey nugget stik 500gr',
            'cireng rujak', 'salam nugget 500gr', 'warisan isi 50', 'belfood sosis isi 30', 'richeese nugget'
        ];

        $products = Product::where('status_aktif', true)
            ->where(function($q) use ($criticalIds, $criticalNames) {
                $q->whereIn('id', $criticalIds)
                  ->orWhereIn(DB::raw('LOWER(TRIM(nama_produk))'), $criticalNames);
            })
            ->orderBy('nama_produk')
            ->get();
        
        // Calculate default seasonal forecast: Semua Produk, Lebaran (March)
        $defaultForecast = $this->calculateSeasonalForecast($products, 'lebaran');
        
        $comparisonData = $defaultForecast['forecast_data'];
        $rangeInfo = $defaultForecast['range_info'];
        
        $seasonLabel = $rangeInfo['label'];
        $historicalPeriod = $rangeInfo['name_prev'];
        $forecastPeriod = $rangeInfo['name_forecast'];
        
        return view('forecasting.index', compact(
            'products', 
            'comparisonData', 
            'seasonLabel', 
            'historicalPeriod', 
            'forecastPeriod'
        ));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string', // can be 'all' or specific product id
            'season' => 'required|in:lebaran,idul_adha,tahun_baru',
            'growth_rate' => 'required|numeric|min:0|max:100',
            'lead_time' => 'required|integer|min:1|max:30',
            'service_level' => 'required|in:90,95,99',
        ]);

        $productId = $request->product_id;
        $season = $request->season;
        $growthRate = $request->growth_rate / 100;
        $leadTime = $request->lead_time;
        $serviceLevel = $request->service_level;

        // Fetch products
        if ($productId === 'all') {
            $criticalIds = [11, 28, 12, 22, 5, 8, 26, 18, 30, 23];
            $criticalNames = [
                'okey sosis 500gr', 'fiesta chicken nugget 450gr', 'jamur enoki', 'meru lapis bogor', 'okey nugget stik 500gr',
                'cireng rujak', 'salam nugget 500gr', 'warisan isi 50', 'belfood sosis isi 30', 'richeese nugget'
            ];

            $products = Product::where('status_aktif', true)
                ->where(function($q) use ($criticalIds, $criticalNames) {
                    $q->whereIn('id', $criticalIds)
                      ->orWhereIn(DB::raw('LOWER(TRIM(nama_produk))'), $criticalNames);
                })
                ->orderBy('nama_produk')
                ->get();
        } else {
            $products = Product::where('id', $productId)->where('status_aktif', true)->get();
            if ($products->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Produk tidak ditemukan.'
                ], 404);
            }
        }

        // Perform calculation
        $resultData = $this->calculateSeasonalForecast($products, $season, $growthRate, $leadTime, $serviceLevel);
        
        $forecastData = $resultData['forecast_data'];
        $rangeInfo = $resultData['range_info'];

        // Get monthly timeline details for the charts
        // We will generate the daily/monthly details for chart representation.
        // For chart representation, we use the selected season's actual sales vs forecast.
        $chartLabels = [];
        $salesData = [];
        $correctedData = [];
        $recData = [];

        // If it is a single product calculation, we can return daily sales data for that month as chart points
        // If it is "all products", we can return top 10 products comparison
        if ($productId !== 'all') {
            // Return daily breakdown for chart
            $product = $products->first();
            $pId = $product->id;
            
            $seasonStart = Carbon::parse($rangeInfo['start']);
            $seasonEnd = Carbon::parse($rangeInfo['end']);
            
            // Reconstruct daily stock levels forward
            $reconstructStart = '2026-01-01';
            $reconstructEnd = '2026-05-31';

            $incoming = IncomingGood::where('product_id', $pId)
                ->whereBetween('tanggal_masuk', [$reconstructStart, $reconstructEnd])
                ->select('tanggal_masuk', DB::raw('SUM(jumlah) as qty'))
                ->groupBy('tanggal_masuk')
                ->pluck('qty', 'tanggal_masuk')
                ->toArray();

            $salesQuery = DB::table('penjualan')
                ->where('product_id', $pId)
                ->select('tanggal_penjualan as tanggal', 'jumlah_terjual as qty');

            $outgoingQuery = DB::table('barang_keluar')
                ->where('product_id', $pId)
                ->where('jenis_keluar', 'penjualan')
                ->select('tanggal_keluar as tanggal', 'jumlah as qty');

            $combinedOutgoing = DB::query()
                ->fromSub($salesQuery->unionAll($outgoingQuery), 'combined')
                ->select('tanggal', DB::raw('SUM(qty) as qty'))
                ->groupBy('tanggal')
                ->pluck('qty', 'tanggal')
                ->toArray();

            $stock = 0;
            $dailyStock = [];
            $period = CarbonPeriod::create($reconstructStart, $reconstructEnd);
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $inc = isset($incoming[$dateStr]) ? floatval($incoming[$dateStr]) : 0;
                $out = isset($combinedOutgoing[$dateStr]) ? floatval($combinedOutgoing[$dateStr]) : 0;

                $stock = $stock + $inc - $out;
                if ($stock < 0) {
                    $stock = 0;
                }
                $dailyStock[$dateStr] = $stock;
            }

            // Daily loop inside the season
            $seasonPeriod = CarbonPeriod::create($seasonStart, $seasonEnd);
            
            $singleForecast = $forecastData[0];
            $daysInMonth = $rangeInfo['days'];
            $dailyAvgSales = $singleForecast['sales_actual'] / $daysInMonth;
            
            // Safety stock daily component
            $dailySafetyStock = $singleForecast['safety_stock'] / $daysInMonth;

            foreach ($seasonPeriod as $date) {
                $dateStr = $date->format('Y-m-d');
                $out = isset($combinedOutgoing[$dateStr]) ? floatval($combinedOutgoing[$dateStr]) : 0;
                $s = isset($dailyStock[$dateStr]) ? $dailyStock[$dateStr] : 0;
                
                $chartLabels[] = $date->locale('id')->translatedFormat('d M');
                $salesData[] = $out;
                
                // If stock was out, corrected demand has active daily average. Otherwise it is actual sales.
                $correctedDemandVal = ($s <= 0 && $out <= 0) ? $dailyAvgSales : $out;
                $correctedData[] = round($correctedDemandVal);
                
                // Forecast daily point
                $dailyForecastVal = $correctedDemandVal * (1 + $growthRate) + $dailySafetyStock;
                $recData[] = round($dailyForecastVal);
            }
        } else {
            // For all products, chart will compare top 10 products with highest sales in that season
            usort($forecastData, function ($a, $b) {
                return $b['sales_actual'] <=> $a['sales_actual'];
            });
            
            $topProducts = array_slice($forecastData, 0, 10);
            foreach ($topProducts as $item) {
                $chartLabels[] = strlen($item['nama']) > 15 ? substr($item['nama'], 0, 15) . '...' : $item['nama'];
                $salesData[] = $item['sales_actual'];
                $correctedData[] = $item['corrected_demand'];
                $recData[] = $item['hasil_ramalan'];
            }
        }

        $totalSales = array_sum(array_column($forecastData, 'sales_actual'));
        $totalLostSales = array_sum(array_column($forecastData, 'lost_sales'));
        $totalCorrected = array_sum(array_column($forecastData, 'corrected_demand'));
        $totalForecast = array_sum(array_column($forecastData, 'hasil_ramalan'));

        return response()->json([
            'status' => 'success',
            'season_label' => $rangeInfo['label'],
            'historical_period' => $rangeInfo['name_prev'],
            'forecast_period' => $rangeInfo['name_forecast'],
            'is_all' => ($productId === 'all'),
            'totals' => [
                'sales' => intval(round($totalSales)),
                'lost_sales' => intval(round($totalLostSales)),
                'corrected' => intval(round($totalCorrected)),
                'forecast' => intval(round($totalForecast)),
            ],
            'chart' => [
                'labels' => $chartLabels,
                'sales' => $salesData,
                'corrected' => $correctedData,
                'recommendation' => $recData,
            ],
            'forecast_data' => $forecastData
        ]);
    }

    private function calculateSeasonalForecast($products, $season, $growthRate = 0.10, $leadTime = 3, $serviceLevel = 95)
    {
        // Define date ranges
        $ranges = [
            'lebaran' => [
                'start' => '2026-03-01',
                'end' => '2026-03-31',
                'name_prev' => 'Maret 2026 (Lebaran 2026)',
                'name_forecast' => 'Maret 2027 (Lebaran 2027)',
                'label' => 'Lebaran',
                'days' => 31
            ],
            'idul_adha' => [
                'start' => '2026-05-01',
                'end' => '2026-05-31',
                'name_prev' => 'Mei 2026 (Idul Adha 2026)',
                'name_forecast' => 'Mei 2027 (Idul Adha 2027)',
                'label' => 'Idul Adha',
                'days' => 31
            ],

            'tahun_baru' => [
                'start' => '2026-01-01',
                'end' => '2026-01-31',
                'name_prev' => 'Januari 2026 (Tahun Baru 2026)',
                'name_forecast' => 'Januari 2027 (Tahun Baru 2027)',
                'label' => 'Tahun Baru',
                'days' => 31
            ],
        ];

        $selectedRange = $ranges[$season] ?? $ranges['lebaran'];
        $seasonStart = Carbon::parse($selectedRange['start']);
        $seasonEnd = Carbon::parse($selectedRange['end']);
        $daysInMonth = $selectedRange['days'];

        // Eager load all transactions for the entire range to prevent N+1 query problem
        $reconstructStart = '2026-01-01';
        $reconstructEnd = '2026-05-31';

        $incomingGoods = IncomingGood::whereBetween('tanggal_masuk', [$reconstructStart, $reconstructEnd])
            ->select('product_id', 'tanggal_masuk', DB::raw('SUM(jumlah) as qty'))
            ->groupBy('product_id', 'tanggal_masuk')
            ->get()
            ->groupBy('product_id');

        $sales = Sale::whereBetween('tanggal_penjualan', [$reconstructStart, $reconstructEnd])
            ->select('product_id', 'tanggal_penjualan', DB::raw('SUM(jumlah_terjual) as qty'))
            ->groupBy('product_id', 'tanggal_penjualan')
            ->get()
            ->groupBy('product_id');

        $outgoingGoods = OutgoingGood::where('jenis_keluar', 'penjualan')
            ->whereBetween('tanggal_keluar', [$reconstructStart, $reconstructEnd])
            ->select('product_id', 'tanggal_keluar', DB::raw('SUM(jumlah) as qty'))
            ->groupBy('product_id', 'tanggal_keluar')
            ->get()
            ->groupBy('product_id');

        $forecastData = [];
        $criticalProductIds = [11, 28, 12, 22, 5, 8, 26, 18, 30, 23]; // 10 main critical products

        $period = CarbonPeriod::create($reconstructStart, $reconstructEnd);

        foreach ($products as $product) {
            $pId = $product->id;

            // Group transactions by date for this product (format keys strictly to Y-m-d to avoid Carbon time-casting issues)
            $pIncoming = [];
            if (isset($incomingGoods[$pId])) {
                foreach ($incomingGoods[$pId] as $row) {
                    $pIncoming[Carbon::parse($row->tanggal_masuk)->format('Y-m-d')] = $row->qty;
                }
            }
            $pSales = [];
            if (isset($sales[$pId])) {
                foreach ($sales[$pId] as $row) {
                    $pSales[Carbon::parse($row->tanggal_penjualan)->format('Y-m-d')] = $row->qty;
                }
            }
            $pOutgoing = [];
            if (isset($outgoingGoods[$pId])) {
                foreach ($outgoingGoods[$pId] as $row) {
                    $pOutgoing[Carbon::parse($row->tanggal_keluar)->format('Y-m-d')] = $row->qty;
                }
            }

            // Combine sales and outgoing
            $pCombinedOutgoing = [];
            foreach ($pSales as $date => $qty) {
                $pCombinedOutgoing[$date] = ($pCombinedOutgoing[$date] ?? 0) + $qty;
            }
            foreach ($pOutgoing as $date => $qty) {
                $pCombinedOutgoing[$date] = ($pCombinedOutgoing[$date] ?? 0) + $qty;
            }

            // Reconstruct daily stock levels FORWARD from Jan 1 to May 31 2026
            $stock = 0;
            $dailyStock = [];
            
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $inc = isset($pIncoming[$dateStr]) ? floatval($pIncoming[$dateStr]) : 0;
                $out = isset($pCombinedOutgoing[$dateStr]) ? floatval($pCombinedOutgoing[$dateStr]) : 0;

                $stock = $stock + $inc - $out;
                if ($stock < 0) {
                    $stock = 0;
                }
                $dailyStock[$dateStr] = $stock;
            }

            // Calculate seasonal metrics inside the selected season month
            $salesActual = 0;
            $stockoutDays = 0;
            $maxDailySales = 0;

            $seasonPeriod = CarbonPeriod::create($seasonStart, $seasonEnd);
            foreach ($seasonPeriod as $date) {
                $dateStr = $date->format('Y-m-d');
                $out = isset($pCombinedOutgoing[$dateStr]) ? floatval($pCombinedOutgoing[$dateStr]) : 0;
                $s = isset($dailyStock[$dateStr]) ? $dailyStock[$dateStr] : 0;

                $salesActual += $out;
                if ($out > $maxDailySales) {
                    $maxDailySales = $out;
                }
                if ($s <= 0) {
                    $stockoutDays++;
                }
            }

            // Lost sales calculation
            $activeDays = $daysInMonth - $stockoutDays;
            if ($activeDays <= 0) {
                $activeDays = 1;
            }

            $lostSales = 0;
            if ($stockoutDays > 0 && $salesActual > 0) {
                $adr = $salesActual / $activeDays;
                $lostSales = $adr * $stockoutDays;
            }

            $correctedDemand = $salesActual + $lostSales;
            $projectedDemand = $correctedDemand * (1 + $growthRate);

            // Safety stock: (Max Daily - Average Daily) * Lead Time
            $averageDailySales = $salesActual / $daysInMonth;
            $safetyStock = ($maxDailySales - $averageDailySales) * $leadTime;
            if ($safetyStock < 0) {
                $safetyStock = 0;
            }

            $hasilRamalan = $projectedDemand + $safetyStock;

            $forecastData[] = [
                'id' => $product->id,
                'kode' => $product->kode_produk,
                'nama' => $product->nama_produk,
                'is_critical' => in_array($product->id, $criticalProductIds) || in_array(strtolower(trim($product->nama_produk)), [
                    'okey sosis 500gr', 'fiesta chicken nugget 450gr', 'jamur enoki', 'meru lapis bogor', 'okey nugget stik 500gr',
                    'cireng rujak', 'salam nugget 500gr', 'warisan isi 50', 'belfood sosis isi 30', 'richeese nugget'
                ]),
                'sales_actual' => intval(round($salesActual)),
                'stockout_days' => intval($stockoutDays),
                'lost_sales' => intval(round($lostSales)),
                'corrected_demand' => intval(round($correctedDemand)),
                'safety_stock' => intval(round($safetyStock)),
                'projected_demand' => intval(round($projectedDemand)),
                'hasil_ramalan' => intval(round($hasilRamalan)),
            ];
        }

        return [
            'forecast_data' => $forecastData,
            'range_info' => $selectedRange,
        ];
    }
}
