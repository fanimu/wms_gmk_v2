<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Gudang;
use App\Http\Requests\ProdukRequest;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Controller untuk mengelola data Produk.
 */
class ProdukController extends Controller
{
    protected ActivityLogService $activityLog;

    public function __construct(ActivityLogService $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    /**
     * Menampilkan daftar produk.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategoriId = $request->input('kategori_id');
        $gudangId = $request->input('gudang_id');
        $gender = $request->input('gender');
        $filterAktif = $request->input('is_active');

        $query = Produk::with(['kategori', 'gudang']);

        if ($search) {
            $query->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
        }

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        if ($gudangId) {
            $query->where('gudang_id', $gudangId);
        }

        if ($gender) {
            $query->where('gender', $gender);
        }

        if ($filterAktif !== null) {
            $query->where('is_active', $filterAktif);
        }

        $produks = $query->paginate(20)->withQueryString();

        return view('produk.index', array_merge(
            compact('produks', 'search', 'kategoriId', 'gudangId', 'gender', 'filterAktif'),
            $this->dropdownData()
        ));
    }

    /**
     * Menampilkan form tambah produk.
     */
    public function create()
    {
        
        return view('produk.create', $this->dropdownData());
    }

    /**
     * Menyimpan data produk baru.
     */
    public function store(ProdukRequest $request)
    {
        try {
            $data = $request->validated();
            $data['is_active'] = $request->has('is_active');
            
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/produk'), $filename);
                $data['gambar'] = 'uploads/produk/' . $filename;
            }
            
            $produk = Produk::create($data);

            $this->activityLog->log('CREATE', 'Produk', 'Menambahkan produk baru: ' . $produk->nama_produk, $produk);

            return redirect()->route('produk.index')->with('success', 'Data produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menambahkan data produk: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail produk.
     */
    public function show(Produk $produk)
    {
        $produk->load(['kategori', 'gudang']);
        return view('produk.show', compact('produk'));
    }

    /**
     * Menampilkan form edit produk.
     */
    public function edit(Produk $produk)
    {
        
        return view('produk.edit', array_merge(compact('produk'), $this->dropdownData()));
    }

    /**
     * Memperbarui data produk.
     */
    public function update(ProdukRequest $request, Produk $produk)
    {
        try {
            $data = $request->validated();
            $data['is_active'] = $request->has('is_active');
            $oldData = $produk->toArray();

            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada
                if ($produk->gambar && file_exists(public_path($produk->gambar))) {
                    unlink(public_path($produk->gambar));
                }
                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/produk'), $filename);
                $data['gambar'] = 'uploads/produk/' . $filename;
            }

            $produk->update($data);
            $newData = $produk->fresh()->toArray();

            $this->activityLog->log('UPDATE', 'Produk', 'Memperbarui data produk: ' . $produk->nama_produk, $produk, $oldData, $newData);

            return redirect()->route('produk.index')->with('success', 'Data produk berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data produk: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data produk (soft delete).
     */
    public function destroy(Produk $produk)
    {
        try {
            $nama = $produk->nama_produk;
            $produk->delete();

            $this->activityLog->log('DELETE', 'Produk', 'Menghapus data produk: ' . $nama);

            return redirect()->route('produk.index')->with('success', 'Data produk berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data produk: ' . $e->getMessage());
        }
    }

    /**
     * Data dropdown (kategori & gudang) untuk form dan filter produk.
     *
     * @return array{kategoriList: \Illuminate\Support\Collection, gudangList: \Illuminate\Support\Collection}
     */
    private function dropdownData(): array
    {
        return [
            'kategoriList' => Kategori::orderBy('nama_kategori')->orderBy('sub_kategori')->pluck('nama_kategori', 'id'),
            'gudangList'   => Gudang::active()->orderBy('nama_gudang')->pluck('nama_gudang', 'id'),
        ];
    }
}
