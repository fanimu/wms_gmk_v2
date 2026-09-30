<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Gudang;
use App\Models\Supplier;
use App\Models\Transaksi;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * Controller untuk halaman Dashboard.
 * Menampilkan ringkasan data dan statistik utama WMS.
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard dengan ringkasan data.
     */
    public function index()
    {
        $stats = [
            'total_produk'     => Produk::active()->count(),
            'total_gudang'     => Gudang::active()->count(),
            'total_supplier'   => Supplier::active()->count(),
            'stok_rendah'      => Produk::active()->stokRendah()->count(),
            'transaksi_hari_ini' => Transaksi::whereDate('tanggal', today())->count(),
            'order_pending'    => Order::byStatus('pending')->count(),
        ];

        // Transaksi terbaru (5 terakhir)
        $transaksiTerbaru = Transaksi::with(['user', 'gudang'])
            ->latest()
            ->take(5)
            ->get();

        // Aktivitas terbaru (10 terakhir)
        $aktivitasTerbaru = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Produk stok rendah (10 teratas)
        $produkStokRendah = Produk::active()
            ->stokRendah()
            ->with('gudang')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'stats',
            'transaksiTerbaru',
            'aktivitasTerbaru',
            'produkStokRendah'
        ));
    }
}
