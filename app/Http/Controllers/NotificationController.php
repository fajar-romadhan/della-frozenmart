<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // Automatically mark all as read when viewing the notifications page
        Notification::where('user_id', auth()->id())
            ->where('status_baca', false)
            ->update(['status_baca' => true]);

        $today = \Carbon\Carbon::today();

        // 1. Get latest analysis for all products
        $latestAnalyses = \App\Models\InventoryAnalysis::whereIn('id', function($query) {
            $query->selectRaw('MAX(id)')
                ->from('analisa_persediaan')
                ->groupBy('product_id');
        })->get()->keyBy('product_id');

        // 2. Fetch active products
        $allProducts = \App\Models\Product::where('status_aktif', true)
            ->with('category')
            ->get();

        $notificationProducts = collect();

        foreach ($allProducts as $product) {
            $analysis = $latestAnalyses->get($product->id);
            
            // Check if status is Warning or Order
            $status = 'Aman';
            if ($analysis) {
                if ($analysis->status_stok === 'Order') {
                    $status = 'Order';
                } elseif ($analysis->status_stok === 'Warning') {
                    $status = 'Warning';
                }
            }
            
            // If status is not Aman, add to the collection
            if ($status !== 'Aman') {
                $notificationProducts->push([
                    'product_id' => $product->id,
                    'kode_produk' => $product->kode_produk,
                    'nama_produk' => $product->nama_produk,
                    'short_code' => $product->short_code,
                    'stok_saat_ini' => $product->stok_saat_ini,
                    'safety_stock' => $analysis ? $analysis->safety_stock : null,
                    'reorder_point' => $analysis ? $analysis->reorder_point : null,
                    'tanggal_kedaluwarsa' => $product->tanggal_kedaluwarsa,
                    'status' => $status,
                ]);
            }
        }

        // Sort: Order first, then Warning
        $statusOrderMap = [
            'Order' => 1,
            'Warning' => 2
        ];
        $notificationProducts = $notificationProducts->sortBy(function($item) use ($statusOrderMap) {
            return $statusOrderMap[$item['status']] ?? 99;
        })->values();

        // Cards data
        $totalNotif = $notificationProducts->count();
        $totalWarning = $notificationProducts->where('status', 'Warning')->count();
        $totalOrder = $notificationProducts->where('status', 'Order')->count();
        $totalExpired = 0;

        // Latest calculation time
        $terakhirDihitungObj = \App\Models\InventoryAnalysis::max('created_at');
        $terakhirDihitung = $terakhirDihitungObj ? \Carbon\Carbon::parse($terakhirDihitungObj) : null;

        // Fetch standard notifications to avoid breaking other layout dependencies
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('notifications.index', compact(
            'notifications',
            'notificationProducts',
            'totalNotif',
            'totalWarning',
            'totalOrder',
            'totalExpired',
            'terakhirDihitung'
        ));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['status_baca' => true]);

        return redirect()->back()->with('success', 'Notifikasi telah ditandai dibaca.');
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('status_baca', false)
            ->update(['status_baca' => true]);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    public function count()
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('status_baca', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Return latest stock notifications for the dropdown widget.
     * Computes real-time status from inventory analysis and expiry dates.
     */
    public function latestDropdown()
    {
        $today = \Carbon\Carbon::today();

        // Get latest analysis per product
        $latestAnalyses = \App\Models\InventoryAnalysis::whereIn('id', function($query) {
            $query->selectRaw('MAX(id)')
                ->from('analisa_persediaan')
                ->groupBy('product_id');
        })->get()->keyBy('product_id');

        // Fetch active products
        $allProducts = \App\Models\Product::where('status_aktif', true)->get();

        // Fetch unread product notification IDs for the logged in user
        $unreadProductIds = \App\Models\Notification::where('user_id', auth()->id())
            ->where('status_baca', false)
            ->whereNotNull('product_id')
            ->pluck('product_id')
            ->toArray();

        $items = collect();

        foreach ($allProducts as $product) {
            $analysis = $latestAnalyses->get($product->id);

            $status = null;
            $judul = '';
            $pesan = '';
            $icon = '';
            $color = '';

            if ($analysis && $analysis->status_stok === 'Order') {
                $status = 'Order';
                $judul = $product->stok_saat_ini <= 0 ? 'Stok Habis' : 'Perlu Order';
                $pesan = $product->stok_saat_ini <= 0 
                    ? $product->nama_produk . ' telah habis (0 pcs).' 
                    : $product->nama_produk . ' tersisa ' . number_format($product->stok_saat_ini, 0, ',', '.') . ' pcs (di bawah ROP).';
                $icon = 'ph-x';
                $color = 'red';
            } elseif ($analysis && $analysis->status_stok === 'Warning') {
                $status = 'Warning';
                $judul = 'Stok Menipis';
                $pesan = $product->nama_produk . ' tersisa ' . number_format($product->stok_saat_ini, 0, ',', '.') . ' pcs (mendekati ROP).';
                $icon = 'ph-warning';
                $color = 'yellow';
            }

            if ($status) {
                $isUnread = in_array($product->id, $unreadProductIds);
                $items->push([
                    'judul' => $judul,
                    'pesan' => $pesan,
                    'icon' => $icon,
                    'color' => $color,
                    'status' => $status,
                    'is_unread' => $isUnread,
                    'created_at' => $analysis ? $analysis->created_at : ($product->updated_at ?? now()),
                ]);
            }
        }

        // Sort by time (newest first)
        $items = $items->sortByDesc('created_at')->values();

        $totalCount = $items->count();

        // Take only the latest 5
        $latest = $items->take(5)->map(function($item) {
            return [
                'judul'     => $item['judul'],
                'pesan'     => $item['pesan'],
                'icon'      => $item['icon'],
                'color'     => $item['color'],
                'status'    => $item['status'],
                'is_unread' => $item['is_unread'],
                'waktu'     => \Carbon\Carbon::parse($item['created_at'])->diffForHumans(),
            ];
        })->values();

        return response()->json([
            'items' => $latest,
            'total' => $totalCount,
            'unread' => count($unreadProductIds),
        ]);
    }
}
