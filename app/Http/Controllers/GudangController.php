<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Http\Requests\GudangRequest;
use App\Services\KodeGeneratorService;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

/**
 * Controller untuk mengelola data Gudang.
 */
class GudangController extends Controller
{
    protected KodeGeneratorService $kodeGenerator;
    protected ActivityLogService $activityLog;

    public function __construct(KodeGeneratorService $kodeGenerator, ActivityLogService $activityLog)
    {
        $this->kodeGenerator = $kodeGenerator;
        $this->activityLog = $activityLog;
    }

    /**
     * Menampilkan daftar gudang.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterAktif = $request->input('is_active');

        $query = Gudang::query();

        if ($search) {
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode_gudang', 'like', "%{$search}%");
        }

        if ($filterAktif !== null) {
            $query->where('is_active', $filterAktif);
        }

        $gudangs = $query->paginate(15)->withQueryString();

        return view('gudang.index', [
            'gudang'      => $gudangs,
            'search'      => $search,
            'filterAktif' => $filterAktif,
        ]);
    }

    /**
     * Menampilkan form tambah gudang.
     */
    public function create()
    {
        return view('gudang.create');
    }

    /**
     * Menyimpan data gudang baru.
     */
    public function store(GudangRequest $request)
    {
        try {
            $data = $request->validated();
            $data['kode_gudang'] = $this->kodeGenerator->generateGudangKode();
            
            $gudang = Gudang::create($data);

            $this->activityLog->log('CREATE_GUDANG', "Menambahkan gudang baru: {$gudang->nama}");

            return redirect()->route('gudang.index')->with('success', 'Data gudang berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menambahkan data gudang: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail gudang.
     */
    public function show(Gudang $gudang)
    {
        $gudang->loadCount(['produk', 'transaksi']);
        return view('gudang.show', compact('gudang'));
    }

    /**
     * Menampilkan form edit gudang.
     */
    public function edit(Gudang $gudang)
    {
        return view('gudang.edit', compact('gudang'));
    }

    /**
     * Memperbarui data gudang.
     */
    public function update(GudangRequest $request, Gudang $gudang)
    {
        try {
            $oldData = $gudang->toArray();
            $gudang->update($request->validated());
            $newData = $gudang->fresh()->toArray();

            $this->activityLog->log('UPDATE_GUDANG', "Memperbarui data gudang: {$gudang->nama}", $oldData, $newData);

            return redirect()->route('gudang.index')->with('success', 'Data gudang berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data gudang: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data gudang (soft delete).
     */
    public function destroy(Gudang $gudang)
    {
        try {
            $nama = $gudang->nama;
            $gudang->delete();

            $this->activityLog->log('DELETE_GUDANG', "Menghapus data gudang: {$nama}");

            return redirect()->route('gudang.index')->with('success', 'Data gudang berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data gudang: ' . $e->getMessage());
        }
    }
}
