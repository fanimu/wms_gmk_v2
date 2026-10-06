<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\SupplierRequest;
use App\Services\KodeGeneratorService;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

/**
 * Controller untuk mengelola data Supplier.
 */
class SupplierController extends Controller
{
    protected KodeGeneratorService $kodeGenerator;
    protected ActivityLogService $activityLog;

    public function __construct(KodeGeneratorService $kodeGenerator, ActivityLogService $activityLog)
    {
        $this->kodeGenerator = $kodeGenerator;
        $this->activityLog = $activityLog;
    }

    /**
     * Menampilkan daftar supplier.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterAktif = $request->input('is_active');

        $query = Supplier::query();

        if ($search) {
            $query->where('nama_supplier', 'like', "%{$search}%")
                  ->orWhere('kode_supplier', 'like', "%{$search}%");
        }

        if ($filterAktif !== null) {
            $query->where('is_active', $filterAktif);
        }

        $suppliers = $query->paginate(15)->withQueryString();

        return view('supplier.index', [
            'supplier'    => $suppliers,
            'search'      => $search,
            'filterAktif' => $filterAktif,
        ]);
    }

    /**
     * Menampilkan form tambah supplier.
     */
    public function create()
    {
        return view('supplier.create');
    }

    /**
     * Menyimpan data supplier baru.
     */
    public function store(SupplierRequest $request)
    {
        try {
            $data = $request->validated();
            $data['kode_supplier'] = $this->kodeGenerator->generateKodeSupplier();
            
            $supplier = Supplier::create($data);

            $this->activityLog->log('CREATE_SUPPLIER', "Menambahkan supplier baru: {$supplier->nama_supplier}");

            return redirect()->route('supplier.index')->with('success', 'Data supplier berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menambahkan data supplier: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail supplier.
     */
    public function show(Supplier $supplier)
    {
        $supplier->loadCount('transaksi');
        return view('supplier.show', compact('supplier'));
    }

    /**
     * Menampilkan form edit supplier.
     */
    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    /**
     * Memperbarui data supplier.
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        try {
            $oldData = $supplier->toArray();
            $supplier->update($request->validated());
            $newData = $supplier->fresh()->toArray();

            $this->activityLog->log('UPDATE_SUPPLIER', "Memperbarui data supplier: {$supplier->nama_supplier}", $oldData, $newData);

            return redirect()->route('supplier.index')->with('success', 'Data supplier berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data supplier: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data supplier (soft delete).
     */
    public function destroy(Supplier $supplier)
    {
        try {
            $nama = $supplier->nama_supplier;
            $supplier->delete();

            $this->activityLog->log('DELETE_SUPPLIER', "Menghapus data supplier: {$nama}");

            return redirect()->route('supplier.index')->with('success', 'Data supplier berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data supplier: ' . $e->getMessage());
        }
    }
}
