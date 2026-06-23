<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    /**
     * Record a user or system activity to the database.
     */
    public static function log(string $tipe, string $judul, string $deskripsi): void
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'tipe' => $tipe,
                'judul' => $judul,
                'deskripsi' => $deskripsi,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Prevent logging failure from breaking the main application request
            \Log::error('Failed to save activity log: ' . $e->getMessage());
        }
    }
}
