<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ImportLog;
use App\Models\IncomingGood;
use App\Models\InventoryAnalysis;
use App\Models\OutgoingGood;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockBatch;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Route to the appropriate dashboard based on user role.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        return match ($user->role) {
            'admin' => $this->admin($request),
            'manager' => $this->manager($request),
            'owner' => $this->owner($request),
            default => abort(403, 'Role tidak dikenali.'),
        };
    }

    /**
     * Admin dashboard with full operational overview.
     */
    private function admin(Request $request)
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Counts
        $totalProduk = Product::where('status_aktif', true)->count();
        $totalSupplier = Supplier::count();

        // Incoming goods today and this month
        $stokMasukHariIni = IncomingGood::whereDate('tanggal_masuk', $today)->sum('jumlah');
        $stokMasukBulanIni = IncomingGood::whereBetween('tanggal_masuk', [$startOfMonth, $today])->sum('jumlah');

        // Product status from latest analysis
        $latestAnalyses = InventoryAnalysis::whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('analisa_persediaan')
                ->groupBy('product_id');
        })->get();

        $statusAman = $latestAnalyses->where('status_stok', 'Aman')->count();
        $statusWarning = $latestAnalyses->where('status_stok', 'Warning')->count();
        $statusOrder = $latestAnalyses->where('status_stok', 'Order')->count();

        // Products near expiry (within 7 days)
        $nearExpiryDate = $today->copy()->addDays(7);
        $produkKedaluwarsa = Product::where('status_aktif', true)
            ->where(function ($query) use ($today, $nearExpiryDate) {
                $query->whereBetween('tanggal_kedaluwarsa', [$today, $nearExpiryDate]);
            })
            ->get();

        // Also check stock batches near expiry
        $batchKedaluwarsa = StockBatch::where('jumlah_sisa', '>', 0)
            ->whereBetween('tanggal_kedaluwarsa', [$today, $nearExpiryDate])
            ->with('product')
            ->get();

        // Recent incoming goods (latest 5)
        $barangMasukTerbaru = IncomingGood::with(['product', 'supplier', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent outgoing goods (latest 5)
        $barangKeluarTerbaru = OutgoingGood::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent import logs (latest 5)
        $importTerbaru = ImportLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Products that need attention (Warning or Order)
        $produkPerhatian = $latestAnalyses->whereIn('status_stok', ['Warning', 'Order'])
            ->load('product');

        // Monthly incoming goods data for chart (last 6 months)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $totalMasuk = IncomingGood::whereYear('tanggal_masuk', $month->year)
                ->whereMonth('tanggal_masuk', $month->month)
                ->sum('jumlah');

            $chartData[] = [
                'bulan' => $month->translatedFormat('M Y'),
                'total' => $totalMasuk,
            ];
        }

        $aktivitasTerbaru = $this->getAktivitasTerbaru();
        $availableYears = $this->getAvailableYears();

        return view('dashboard.admin', compact(
            'totalProduk',
            'totalSupplier',
            'stokMasukHariIni',
            'stokMasukBulanIni',
            'statusAman',
            'statusWarning',
            'statusOrder',
            'produkKedaluwarsa',
            'batchKedaluwarsa',
            'barangMasukTerbaru',
            'barangKeluarTerbaru',
            'importTerbaru',
            'produkPerhatian',
            'chartData',
            'aktivitasTerbaru',
            'availableYears'
        ));
    }

    /**
     * Manager dashboard with transaction and analysis focus.
     */
    private function manager(Request $request)
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Sales summary this month
        $totalPenjualanBulanIni = Sale::whereBetween('tanggal_penjualan', [$startOfMonth, $today])
            ->sum('jumlah_terjual');
        $jumlahTransaksiBulanIni = Sale::whereBetween('tanggal_penjualan', [$startOfMonth, $today])
            ->count();

        // Recent sales import logs (latest 5)
        $importPenjualanTerbaru = ImportLog::where('jenis_import', 'penjualan')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent purchase invoice import logs (latest 5)
        $importFakturTerbaru = ImportLog::where('jenis_import', 'faktur_pembelian')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Products that must be ordered (status Order)
        $produkHarusDipesan = InventoryAnalysis::whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('analisa_persediaan')
                ->groupBy('product_id');
        })
            ->where('status_stok', 'Order')
            ->with('product')
            ->get();

        // Latest analysis results
        $analisisTerbaru = InventoryAnalysis::with('product')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Products recently updated stock
        $produkStokDiperbarui = Product::where('status_aktif', true)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $aktivitasTerbaru = $this->getAktivitasTerbaru();
        $availableYears = $this->getAvailableYears();

        return view('dashboard.manager', compact(
            'totalPenjualanBulanIni',
            'jumlahTransaksiBulanIni',
            'importPenjualanTerbaru',
            'importFakturTerbaru',
            'produkHarusDipesan',
            'analisisTerbaru',
            'produkStokDiperbarui',
            'aktivitasTerbaru',
            'availableYears'
        ));
    }

    /**
     * Owner dashboard with high-level business overview.
     */
    private function owner(Request $request)
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfPrevMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfPrevMonth = Carbon::now()->subMonth()->endOfMonth();

        // Sales summary
        $penjualanBulanIni = Sale::whereBetween('tanggal_penjualan', [$startOfMonth, $today])
            ->sum('jumlah_terjual');
        $penjualanBulanLalu = Sale::whereBetween('tanggal_penjualan', [$startOfPrevMonth, $endOfPrevMonth])
            ->sum('jumlah_terjual');

        // Incoming goods this month
        $barangMasukBulanIni = IncomingGood::whereBetween('tanggal_masuk', [$startOfMonth, $today])
            ->sum('jumlah');

        // Top 5 best selling products
        $produkTerlaris = Sale::whereBetween('tanggal_penjualan', [$startOfMonth, $today])
            ->selectRaw('product_id, SUM(jumlah_terjual) as total_terjual')
            ->groupBy('product_id')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->with('product')
            ->get();

        // Products that need to be ordered (status Order)
        $produkPerluDipesan = InventoryAnalysis::whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('analisa_persediaan')
                ->groupBy('product_id');
        })
            ->where('status_stok', 'Order')
            ->with('product')
            ->paginate(5, ['*'], 'rop_page')
            ->onEachSide(1);

        // Stock status overview
        $latestAnalyses = InventoryAnalysis::whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('analisa_persediaan')
                ->groupBy('product_id');
        })->get();

        $statusOverview = [
            'aman' => $latestAnalyses->where('status_stok', 'Aman')->count(),
            'warning' => $latestAnalyses->where('status_stok', 'Warning')->count(),
            'order' => $latestAnalyses->where('status_stok', 'Order')->count(),
        ];

        $aktivitasTerbaru = $this->getAktivitasTerbaru();
        $availableYears = $this->getAvailableYears();

        return view('dashboard.owner', compact(
            'penjualanBulanIni',
            'penjualanBulanLalu',
            'barangMasukBulanIni',
            'produkTerlaris',
            'produkPerluDipesan',
            'statusOverview',
            'aktivitasTerbaru',
            'availableYears'
        ));
    }

    /**
     * AJAX Endpoint to fetch dynamic sales data.
     */
    public function getSalesData(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $month = $request->query('month');

        if ($month) {
            // Daily sales for a specific month
            $sales = Sale::whereYear('tanggal_penjualan', $year)
                ->whereMonth('tanggal_penjualan', $month)
                ->selectRaw('DAY(tanggal_penjualan) as day, SUM(jumlah_terjual) as total')
                ->groupBy('day')
                ->orderBy('day', 'asc')
                ->get();

            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
            $labels = [];
            $data = [];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = 'Tgl ' . $d;
                $found = $sales->firstWhere('day', $d);
                $data[] = $found ? (int)$found->total : 0;
            }
        } else {
            // Monthly sales for a specific year
            $sales = Sale::whereYear('tanggal_penjualan', $year)
                ->selectRaw('MONTH(tanggal_penjualan) as month, SUM(jumlah_terjual) as total')
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $data = [];

            for ($m = 1; $m <= 12; $m++) {
                $found = $sales->firstWhere('month', $m);
                $data[] = $found ? (int)$found->total : 0;
            }
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    /**
     * Get unified recent activities feed.
     */
    private function getAktivitasTerbaru()
    {
        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $aktivitas = collect();

        foreach ($logs as $log) {
            $icon = 'ph ph-info';
            $warna = 'secondary';

            switch ($log->tipe) {
                case 'incoming':
                    $icon = 'ph ph-arrow-down-left';
                    $warna = 'success';
                    break;
                case 'outgoing':
                    $icon = 'ph ph-arrow-up-right';
                    $warna = 'danger';
                    break;
                case 'opname':
                    $icon = 'ph ph-scales';
                    $warna = 'warning';
                    break;
                case 'import':
                    $icon = 'ph ph-upload-simple';
                    $warna = 'primary';
                    break;
                case 'export':
                    $icon = 'ph ph-download-simple';
                    $warna = 'info';
                    break;
                case 'create':
                    $icon = 'ph ph-plus-circle';
                    $warna = 'success';
                    break;
                case 'update':
                    $icon = 'ph ph-pencil-simple';
                    $warna = 'warning';
                    break;
                case 'delete':
                    $icon = 'ph ph-trash';
                    $warna = 'danger';
                    break;
            }

            $aktivitas->push([
                'tipe' => $log->tipe,
                'judul' => $log->judul,
                'deskripsi' => $log->deskripsi,
                'pengguna' => $log->user->name ?? 'Sistem',
                'waktu' => $log->created_at,
                'icon' => $icon,
                'warna' => $warna,
            ]);
        }

        return $aktivitas;
    }

    /**
     * Get unique years of sales available.
     */
    private function getAvailableYears()
    {
        $years = Sale::selectRaw('YEAR(tanggal_penjualan) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        if (empty($years)) {
            $years = [date('Y')];
        }
        return $years;
    }
}
