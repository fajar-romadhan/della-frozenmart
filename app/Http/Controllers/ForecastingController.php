<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\IncomingGood;
use App\Models\OutgoingGood;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ForecastingController extends Controller
{
    public function index()
    {
        $products = Product::where('status_aktif', true)->orderBy('nama_produk')->get();
        
        $latestSale = OutgoingGood::latest('tanggal_keluar')->first();
        $historicalYear = $latestSale ? Carbon::parse($latestSale->tanggal_keluar)->year : Carbon::now()->year;
        $forecastYear = $historicalYear + 1;
        
        return view('forecasting.index', compact('products', 'historicalYear', 'forecastYear'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:produk,id',
            'growth_rate' => 'required|numeric|min:0|max:100',
            'lead_time' => 'required|integer|min:1|max:30',
            'service_level' => 'required|in:90,95,99',
        ]);

        $productId = $request->product_id;
        $growthRate = $request->growth_rate / 100;
        $leadTime = $request->lead_time;
        $serviceLevel = $request->service_level;

        // Z-Score mapping for statistical safety stock
        $zScoreMap = [
            '90' => 1.28,
            '95' => 1.65,
            '99' => 2.33,
        ];
        $zScore = $zScoreMap[$serviceLevel];

        $latestSale = OutgoingGood::latest('tanggal_keluar')->first();
        $historicalYear = $latestSale ? Carbon::parse($latestSale->tanggal_keluar)->year : Carbon::now()->year;
        $forecastYear = $historicalYear + 1;

        $product = Product::findOrFail($productId);

        // Detect the active range of sales for the product in the historical year
        $minDateStr = OutgoingGood::where('product_id', $productId)->whereYear('tanggal_keluar', $historicalYear)->min('tanggal_keluar');
        $maxDateStr = OutgoingGood::where('product_id', $productId)->whereYear('tanggal_keluar', $historicalYear)->max('tanggal_keluar');

        if (!$minDateStr || !$maxDateStr) {
            // Fallback range if no sales recorded yet
            $minDateStr = $historicalYear . '-01-01';
            $maxDateStr = $historicalYear . '-05-31';
        }

        $minDate = Carbon::parse($minDateStr)->startOfMonth();
        $maxDate = Carbon::parse($maxDateStr)->endOfMonth();

        // Get daily incoming and outgoing quantities
        $incoming = IncomingGood::where('product_id', $productId)
            ->select('tanggal_masuk', DB::raw('SUM(jumlah) as qty'))
            ->groupBy('tanggal_masuk')
            ->pluck('qty', 'tanggal_masuk')
            ->toArray();

        $outgoing = OutgoingGood::where('product_id', $productId)
            ->select('tanggal_keluar', DB::raw('SUM(jumlah) as qty'))
            ->groupBy('tanggal_keluar')
            ->pluck('qty', 'tanggal_keluar')
            ->toArray();

        // Reconstruct daily stock levels from today backwards to minDate
        $currentStock = $product->stok_saat_ini;
        $today = Carbon::today();
        
        $dailyStock = [];
        $tempStock = $currentStock;

        for ($date = clone $today; $date->greaterThanOrEqualTo($minDate); $date->subDay()) {
            $dateStr = $date->format('Y-m-d');
            $dailyStock[$dateStr] = $tempStock;

            $incQty = isset($incoming[$dateStr]) ? floatval($incoming[$dateStr]) : 0;
            $outQty = isset($outgoing[$dateStr]) ? floatval($outgoing[$dateStr]) : 0;
            $tempStock = $tempStock - $incQty + $outQty;
        }

        // Group into monthly data
        $monthlyData = [];
        $totalSalesHistorical = 0;
        $totalCorrectedHistorical = 0;

        $tempMonth = clone $minDate;
        $monthsList = [];
        while ($tempMonth->lessThanOrEqualTo($maxDate)) {
            $monthsList[] = [
                'num' => $tempMonth->month,
                'year' => $tempMonth->year,
                'name' => $tempMonth->locale('id')->translatedFormat('F Y'),
                'start' => $tempMonth->copy()->startOfMonth(),
                'end' => $tempMonth->copy()->endOfMonth(),
            ];
            $tempMonth->addMonth();
        }

        foreach ($monthsList as $monthInfo) {
            $mNum = $monthInfo['num'];
            $mYear = $monthInfo['year'];
            $mName = $monthInfo['name'];
            $monthStartDate = $monthInfo['start'];
            $monthEndDate = $monthInfo['end'];

            $daysInMonth = $monthStartDate->diffInDays($monthEndDate) + 1;

            // Calculate actual sales
            $salesInMonth = OutgoingGood::where('product_id', $productId)
                ->whereYear('tanggal_keluar', $mYear)
                ->whereMonth('tanggal_keluar', $mNum)
                ->sum('jumlah');

            // Calculate stockout days
            $stockoutDays = 0;
            for ($d = clone $monthStartDate; $d->lessThanOrEqualTo($monthEndDate); $d->addDay()) {
                $dStr = $d->format('Y-m-d');
                $stock = isset($dailyStock[$dStr]) ? $dailyStock[$dStr] : 0;

                // Skip dates in the future
                if ($d->greaterThan($today)) {
                    continue;
                }
                if ($stock <= 0) {
                    $stockoutDays++;
                }
            }

            $activeDays = $daysInMonth - $stockoutDays;
            if ($activeDays <= 0) {
                $activeDays = 1;
            }

            // Unconstrain demand
            $correctedDemand = $salesInMonth;
            $lostSales = 0;
            if ($stockoutDays > 0 && $salesInMonth > 0) {
                $adr = $salesInMonth / $activeDays;
                $lostSales = $adr * $stockoutDays;
                $correctedDemand = $salesInMonth + $lostSales;
            }

            $monthlyData[] = [
                'name' => $mName,
                'sales' => intval($salesInMonth),
                'stockout_days' => $stockoutDays,
                'lost_sales' => intval(round($lostSales)),
                'corrected_demand' => intval(round($correctedDemand)),
            ];

            $totalSalesHistorical += $salesInMonth;
            $totalCorrectedHistorical += $correctedDemand;
        }

        $numMonths = count($monthlyData);
        if ($numMonths <= 0) {
            $numMonths = 1;
        }

        // Average corrected monthly
        $avgCorrectedMonthly = $totalCorrectedHistorical / $numMonths;
        if ($avgCorrectedMonthly <= 0) {
            $avgCorrectedMonthly = 1;
        }

        // Calculate Seasonal Index for each month
        foreach ($monthlyData as &$data) {
            $data['seasonal_index'] = round($data['corrected_demand'] / $avgCorrectedMonthly, 4);
        }
        unset($data);

        // Projections
        $projectedTotalNext = $totalCorrectedHistorical * (1 + $growthRate);
        $projectedAvgMonthlyNext = $projectedTotalNext / $numMonths;

        // Standard Deviation
        $sumSquares = 0;
        foreach ($monthlyData as $data) {
            $sumSquares += pow($data['corrected_demand'] - $avgCorrectedMonthly, 2);
        }
        $stdDev = $numMonths > 1 ? sqrt($sumSquares / ($numMonths - 1)) : 0;

        // Safety Stock calculation
        $safetyStockVal = $zScore * $stdDev * sqrt($leadTime / 30);

        // Calculate forecast and recommendation for next year
        foreach ($monthlyData as &$data) {
            $forecastedDemand = $projectedAvgMonthlyNext * $data['seasonal_index'];
            $recStock = $forecastedDemand + $safetyStockVal;

            $data['forecast_2027'] = intval(round($forecastedDemand));
            $data['safety_stock_2027'] = intval(round($safetyStockVal));
            $data['recommendation_2027'] = intval(round($recStock));
        }
        unset($data);

        return response()->json([
            'status' => 'success',
            'product_id' => $product->id,
            'product_name' => $product->nama_produk,
            'growth_rate' => $growthRate * 100,
            'lead_time' => $leadTime,
            'service_level' => $serviceLevel,
            'z_score' => $zScore,
            'std_dev' => round($stdDev, 2),
            'safety_stock_global' => intval(round($safetyStockVal)),
            'monthly_data' => $monthlyData,
            'historical_year' => $historicalYear,
            'forecast_year' => $forecastYear,
            'totals' => [
                'sales' => intval(round($totalSalesHistorical)),
                'corrected' => intval(round($totalCorrectedHistorical)),
            ]
        ]);
    }
}
