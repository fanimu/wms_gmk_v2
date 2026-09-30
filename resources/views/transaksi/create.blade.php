@extends('layouts.app')

@section('page-title', 'Buat Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item text-sm"><a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline">Dashboard</a></li>
    <li class="breadcrumb-item text-sm"><a href="{{ route('transaksi.index') }}" class="text-blue-500 hover:underline">/ Transaksi</a></li>
    <li class="breadcrumb-item text-sm text-gray-500 active" aria-current="page">/ Buat Transaksi</li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6" x-data="transactionForm()">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Informasi Transaksi</h2>
        
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <strong class="font-bold">Ada kesalahan!</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('transaksi.store') }}" method="POST">
            @csrf
            
            <!-- Header -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis Transaksi <span class="text-red-500">*</span></label>
                    <select name="jenis" id="jenis" x-model="jenis" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Jenis</option>
                        <option value="MASUK">MASUK</option>
                        <option value="KELUAR">KELUAR</option>
                        <option value="RETUR">RETUR</option>
                        <option value="ADJUSTMENT">ADJUSTMENT</option>
                    </select>
                </div>

                <div>
                    <label for="gudang_id" class="block text-sm font-medium text-gray-700">Gudang <span class="text-red-500">*</span></label>
                    <select name="gudang_id" id="gudang_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Gudang</option>
                        @foreach($gudangList ?? [] as $gudang)
                            <option value="{{ $gudang->id }}" {{ old('gudang_id') == $gudang->id ? 'selected' : '' }}>{{ $gudang->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="jenis === 'MASUK'">
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier <span x-show="jenis === 'MASUK'" class="text-red-500">*</span></label>
                    <select name="supplier_id" id="supplier_id" :required="jenis === 'MASUK'" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Supplier</option>
                        @foreach($supplierList ?? [] as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <hr class="mb-6">

            <!-- Detail / Line Items -->
            <div class="mb-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Detail Barang</h2>
                <button type="button" @click="addItem()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1 px-3 rounded shadow text-sm transition duration-150 ease-in-out">
                    + Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto mb-6">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk <span class="text-red-500">*</span></th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Qty <span class="text-red-500">*</span></th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Harga Satuan <span class="text-red-500">*</span></th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="px-4 py-2">
                                    <select :name="'items['+index+'][produk_id]'" x-model="item.produk_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">Pilih Produk</option>
                                        @foreach($produkList ?? [] as $produk)
                                            <option value="{{ $produk->id }}">{{ $produk->kode }} - {{ $produk->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" :name="'items['+index+'][qty]'" x-model="item.qty" min="1" required class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" :name="'items['+index+'][harga_satuan]'" x-model="item.harga_satuan" min="0" step="0.01" required class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" :name="'items['+index+'][catatan]'" x-model="item.catatan" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-800 focus:outline-none" title="Hapus Baris">
                                        <svg class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0">
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500 text-sm bg-gray-50">Belum ada barang yang ditambahkan. Klik "+ Tambah Baris" untuk memulai.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end gap-4 mt-8">
                <a href="{{ route('transaksi.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded shadow transition duration-150 ease-in-out">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150 ease-in-out">Simpan Draft</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('transactionForm', () => ({
            jenis: '{{ old('jenis') }}',
            items: @json(old('items', [['produk_id' => '', 'qty' => 1, 'harga_satuan' => 0, 'catatan' => '']])),
            
            addItem() {
                this.items.push({
                    produk_id: '',
                    qty: 1,
                    harga_satuan: 0,
                    catatan: ''
                });
            },
            
            removeItem(index) {
                this.items.splice(index, 1);
            }
        }))
    })
</script>
@endsection
