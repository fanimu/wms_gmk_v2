<?php

namespace App\Services;

use App\Models\Transaksi;
use App\Models\User;
use App\Models\Produk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class StokService
{
    protected ActivityLogService $activityLog;

    public function __construct(ActivityLogService $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    /**
     * Loop through transaksi details and INCREASE product stock
     */
    public function prosesTransaksiMasuk(Transaksi $transaksi): void
    {
        foreach ($transaksi->detail as $detail) {
            $produk = Produk::lockForUpdate()->findOrFail($detail->produk_id);
            $produk->increment('stok', $detail->qty);
        }
    }

    /**
     * Loop through transaksi details and DECREASE product stock
     */
    public function prosesTransaksiKeluar(Transaksi $transaksi): void
    {
        foreach ($transaksi->detail as $detail) {
            $produk = Produk::lockForUpdate()->findOrFail($detail->produk_id);
            
            if ($produk->stok < $detail->qty) {
                throw new Exception("Stok tidak mencukupi untuk produk: {$produk->nama_produk}");
            }
            
            $produk->decrement('stok', $detail->qty);
        }
    }

    /**
     * Handles return (increase stock back)
     */
    public function prosesRetur(Transaksi $transaksi): void
    {
        foreach ($transaksi->detail as $detail) {
            $produk = Produk::lockForUpdate()->findOrFail($detail->produk_id);
            $produk->increment('stok', $detail->qty);
        }
    }

    /**
     * Direct stock adjustment (bisa positif atau negatif berdasarkan qty yang dimasukkan, jika adjustment menggantikan qty, logika disesuaikan. Di sini asumsi qty adalah delta)
     */
    public function prosesAdjustment(Transaksi $transaksi): void
    {
        foreach ($transaksi->detail as $detail) {
            $produk = Produk::lockForUpdate()->findOrFail($detail->produk_id);
            
            // Asumsi qty pada adjustment adalah selisih (bisa negatif atau positif)
            $produk->increment('stok', $detail->qty);
            
            if ($produk->stok < 0) {
                 throw new Exception("Stok tidak boleh kurang dari 0 untuk produk: {$produk->nama_produk}");
            }
        }
    }

    /**
     * Validate, process stock based on jenis, update status to CONFIRMED, set confirmed_at and confirmed_by, log activity
     */
    public function confirmTransaksi(Transaksi $transaksi, User $user): void
    {
        if ($transaksi->status !== 'DRAFT') {
            throw new Exception("Hanya transaksi dengan status DRAFT yang dapat dikonfirmasi.");
        }

        DB::transaction(function () use ($transaksi, $user) {
            switch ($transaksi->jenis) {
                case 'MASUK':
                    $this->prosesTransaksiMasuk($transaksi);
                    break;
                case 'KELUAR':
                    $this->prosesTransaksiKeluar($transaksi);
                    break;
                case 'RETUR':
                    $this->prosesRetur($transaksi);
                    break;
                case 'ADJUSTMENT':
                    $this->prosesAdjustment($transaksi);
                    break;
                default:
                    throw new Exception("Jenis transaksi tidak valid.");
            }

            $transaksi->update([
                'status' => 'CONFIRMED',
                'confirmed_at' => now(),
                'confirmed_by' => $user->id,
            ]);

            $this->activityLog->log(
                'confirm_transaksi',
                'transaksi',
                "Mengkonfirmasi transaksi {$transaksi->no_transaksi}",
                $transaksi
            );
        });
    }

    /**
     * Can only cancel DRAFT transactions, set status to CANCELLED
     */
    public function cancelTransaksi(Transaksi $transaksi): void
    {
        if ($transaksi->status !== 'DRAFT') {
            throw new Exception("Hanya transaksi dengan status DRAFT yang dapat dibatalkan.");
        }

        DB::transaction(function () use ($transaksi) {
            $transaksi->update([
                'status' => 'CANCELLED'
            ]);

            $this->activityLog->log(
                'cancel_transaksi',
                'transaksi',
                "Membatalkan transaksi {$transaksi->no_transaksi}",
                $transaksi
            );
        });
    }

    /**
     * Reverse stock changes if needed
     */
    public function reverseStok(Transaksi $transaksi): void
    {
        if ($transaksi->status !== 'CONFIRMED') {
            throw new Exception("Hanya transaksi yang telah dikonfirmasi yang dapat di-reverse stoknya.");
        }

        DB::transaction(function () use ($transaksi) {
            switch ($transaksi->jenis) {
                case 'MASUK':
                case 'RETUR':
                    $this->prosesTransaksiKeluar($transaksi); // Kembalikan seperti keluar
                    break;
                case 'KELUAR':
                    $this->prosesTransaksiMasuk($transaksi); // Kembalikan seperti masuk
                    break;
                case 'ADJUSTMENT':
                    foreach ($transaksi->detail as $detail) {
                        $produk = Produk::lockForUpdate()->findOrFail($detail->produk_id);
                        $produk->decrement('stok', $detail->qty);
                    }
                    break;
                default:
                    throw new Exception("Jenis transaksi tidak valid.");
            }

            $transaksi->update([
                'status' => 'CANCELLED'
            ]);

            $this->activityLog->log(
                'reverse_transaksi',
                'transaksi',
                "Membatalkan/Reverse stok transaksi {$transaksi->no_transaksi}",
                $transaksi
            );
        });
    }

    /**
     * Get all products and their stock for a warehouse
     */
    public function getStokByGudang(int $gudangId): Collection
    {
        return Produk::where('gudang_id', $gudangId)->get();
    }

    /**
     * Get products below minimum stock
     */
    public function getProduStokRendah(): Collection
    {
        return Produk::whereColumn('stok', '<', 'stok_minimum')->get();
    }
}
