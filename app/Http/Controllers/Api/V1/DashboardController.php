<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\Disposisi;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        $stats = [
            'total_surat_masuk' => SuratMasuk::count(),
            'total_surat_keluar' => SuratKeluar::count(),
            'total_disposisi' => Disposisi::count(),
            'total_users' => User::count(),
            'surat_masuk_status' => [
                'pending' => SuratMasuk::where('status', 'pending')->count(),
                'processed' => SuratMasuk::where('status', 'processed')->count(),
                'archived' => SuratMasuk::where('status', 'archived')->count(),
            ],
            'surat_keluar_status' => [
                'draft' => SuratKeluar::where('status', 'draft')->count(),
                'sent' => SuratKeluar::where('status', 'sent')->count(),
            ],
            'gender_stats' => [
                'L' => 45, // Sample static as gender not in migration yet
                'P' => 55,
            ]
        ];

        return response()->json($stats);
    }
}
