<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Supplier;
use App\Models\Gudang;
use App\Models\Produk;
use App\Http\Requests\TransaksiRequest;
use App\Services\KodeGeneratorService;
use App\Services\ActivityLogService;
use App\Services\StokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk mengelola data Transaksi.
 */
class TransaksiController extends Controller
{
    protected KodeGeneratorService $kodeGenerator;
    protected ActivityLogService $activityLog;
    protected StokService $stokService;

    public function __construct(
        KodeGeneratorService $kodeGenerator, 
        ActivityLogService $activityLog,
        StokService $stokService
    ) {
        $this->kodeGenerator = $kodeGenerator;
        $this->activityLog = $activityLog;
        $this->stokService = $stokService;
    }

    /**
     * Menampilkan daftar transaksi.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $jenis = $request->input('jenis');
        $status = $request->input('status');
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        $query = Transaksi::with(['supplier', 'gudang', 'user']);

        if ($search) {
            $query->where('kode_transaksi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
        }

        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($dari && $sampai) {
            $query->whereBetween('tanggal', [$dari, $sampai]);
        }

        $transaksis = $query->latest('tanggal')->paginate(20)->withQueryString();

        return view('transaksi.index', compact('transaksis', 'search', 'jenis', 'status', 'dari', 'sampai'));
    }

    /**
     * Menampilkan form tambah transaksi.
     */
    public function create()
    {
        $suppliers = Supplier::active()->get();
        $gudangs = Gudang::active()->get();
        $produks = Produk::active()->get();

        return view('transaksi.create', compact('suppliers', 'gudangs', 'produks'));
    }

    /**
     * Menyimpan data transaksi baru.
     */
    public function store(TransaksiRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['kode_transaksi'] = $this->kodeGenerator->generateTransaksiKode($data['jenis']);
            $data['status'] = 'DRAFT';
            $data['user_id'] = auth()->id();

            $transaksi = Transaksi::create($data);

            if (!empty($data['details'])) {
                foreach ($data['details'] as $detail) {
                    $transaksi->detail()->create([
                        'produk_id' => $detail['produk_id'],
                        'qty' => $detail['qty'],
                        'harga' => $detail['harga'] ?? 0,
                        'catatan' => $detail['catatan'] ?? null,
                    ]);
                }
            }

            $this->activityLog->log('CREATE_TRANSAKSI', "Menambahkan transaksi baru: {$transaksi->kode_transaksi}");

            DB::commit();

            return redirect()->route('transaksi.index')->with('success', 'Data transaksi berhasil ditambahkan (DRAFT).');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menambahkan data transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail transaksi.
     */
    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['supplier', 'gudang', 'user', 'detail.produk']);
        return view('transaksi.show', compact('transaksi'));
    }

    /**
     * Menampilkan form edit transaksi.
     */
    public function edit(Transaksi $transaksi)
    {
        if ($transaksi->status !== 'DRAFT') {
            abort(403, 'Hanya transaksi DRAFT yang bisa diedit.');
        }

        $transaksi->load('detail');
        $suppliers = Supplier::active()->get();
        $gudangs = Gudang::active()->get();
        $produks = Produk::active()->get();

        return view('transaksi.edit', compact('transaksi', 'suppliers', 'gudangs', 'produks'));
    }

    /**
     * Memperbarui data transaksi.
     */
    public function update(TransaksiRequest $request, Transaksi $transaksi)
    {
        if ($transaksi->status !== 'DRAFT') {
            abort(403, 'Hanya transaksi DRAFT yang bisa diedit.');
        }

        try {
            DB::beginTransaction();

            $oldData = $transaksi->toArray();
            $data = $request->validated();
            
            $transaksi->update($data);

            // Sync details
            $transaksi->detail()->delete(); // hapus yang lama
            if (!empty($data['details'])) {
                foreach ($data['details'] as $detail) {
                    $transaksi->detail()->create([
                        'produk_id' => $detail['produk_id'],
                        'qty' => $detail['qty'],
                        'harga' => $detail['harga'] ?? 0,
                        'catatan' => $detail['catatan'] ?? null,
                    ]);
                }
            }
            
            $newData = $transaksi->fresh()->toArray();

            $this->activityLog->log('UPDATE_TRANSAKSI', "Memperbarui transaksi: {$transaksi->kode_transaksi}", $oldData, $newData);

            DB::commit();

            return redirect()->route('transaksi.index')->with('success', 'Data transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data transaksi.
     */
    public function destroy(Transaksi $transaksi)
    {
        if ($transaksi->status !== 'DRAFT') {
            abort(403, 'Hanya transaksi DRAFT yang bisa dihapus.');
        }

        try {
            DB::beginTransaction();
            
            $kode = $transaksi->kode_transaksi;
            $transaksi->detail()->delete();
            $transaksi->delete();

            $this->activityLog->log('DELETE_TRANSAKSI', "Menghapus transaksi: {$kode}");

            DB::commit();

            return redirect()->route('transaksi.index')->with('success', 'Data transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menghapus data transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Mengkonfirmasi transaksi.
     */
    public function confirm(Transaksi $transaksi)
    {
        if ($transaksi->status !== 'DRAFT') {
            return back()->with('error', 'Hanya transaksi DRAFT yang bisa dikonfirmasi.');
        }

        try {
            $this->stokService->confirmTransaksi($transaksi);
            return back()->with('success', 'Transaksi berhasil dikonfirmasi dan stok telah diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengkonfirmasi transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Membatalkan transaksi.
     */
    public function cancel(Transaksi $transaksi)
    {
        if ($transaksi->status === 'DRAFT' || $transaksi->status === 'CANCELLED') {
            return back()->with('error', 'Transaksi tidak dapat dibatalkan.');
        }

        try {
            $this->stokService->cancelTransaksi($transaksi);
            return back()->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
