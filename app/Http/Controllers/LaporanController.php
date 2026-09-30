<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Gudang;
use App\Models\Transaksi;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Controller untuk modul Laporan.
 * Menampilkan berbagai laporan dan ringkasan data.
 */
class LaporanController extends Controller
{
    /**
     * Laporan stok per gudang.
     */
    public function stok(Request $request)
    {
        $gudangId = $request->input('gudang_id');
        $gudangList = Gudang::active()->get();

        $query = Produk::active()->with(['kategori', 'gudang']);

        if ($gudangId) {
            $query->byGudang($gudangId);
        }

        $produk = $query->orderBy('nama_produk')->paginate(25);

        return view('laporan.stok', compact('produk', 'gudangList', 'gudangId'));
    }

    /**
     * Laporan transaksi per periode.
     */
    public function transaksi(Request $request)
    {
        $dari = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->toDateString());
        $jenis = $request->input('jenis');

        $query = Transaksi::with(['supplier', 'gudang', 'user', 'detail'])
            ->byPeriode($dari, $sampai);

        if ($jenis) {
            $query->byJenis($jenis);
        }

        $transaksi = $query->latest('tanggal')->paginate(25);

        // Summary totals
        $summary = [
            'total_masuk'  => (clone $query)->byJenis('MASUK')->count(),
            'total_keluar' => (clone $query)->byJenis('KELUAR')->count(),
            'total_retur'  => (clone $query)->byJenis('RETUR')->count(),
        ];

        return view('laporan.transaksi', compact('transaksi', 'dari', 'sampai', 'jenis', 'summary'));
    }

    /**
     * Laporan penjualan (dari orders).
     */
    public function penjualan(Request $request)
    {
        $dari = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->toDateString());
        $platform = $request->input('platform');

        $query = Order::with(['store', 'items'])
            ->byPeriode($dari, $sampai);

        if ($platform) {
            $query->byPlatform($platform);
        }

        $orders = $query->latest('order_date')->paginate(25);

        // Summary
        $summary = [
            'total_orders'  => (clone $query)->count(),
            'total_revenue' => (clone $query)->sum('total_amount'),
            'total_shopee'  => (clone $query)->byPlatform('shopee')->count(),
            'total_tiktok'  => (clone $query)->byPlatform('tiktok')->count(),
        ];

        return view('laporan.penjualan', compact('orders', 'dari', 'sampai', 'platform', 'summary'));
    }
}
