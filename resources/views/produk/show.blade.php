@extends('layouts.app')

@section('page-title', 'Detail Produk')

@section('breadcrumb')
    <li class="inline-flex items-center">
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">Dashboard</a>
        <svg class="w-3 h-3 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    </li>
    <li class="inline-flex items-center">
        <a href="{{ route('produk.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Master Produk</a>
        <svg class="w-3 h-3 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    </li>
    <li class="inline-flex items-center">
        <span class="text-sm font-medium text-gray-900">Detail Produk</span>
    </li>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-5xl mx-auto">
    <div class="flex flex-col md:flex-row gap-8">
        
        <!-- Kolom Gambar -->
        <div class="w-full md:w-1/3 flex flex-col items-center">
            @if($produk->gambar)
                <img src="{{ asset($produk->gambar) }}" alt="{{ $produk->nama_produk }}" class="w-full h-auto object-cover rounded-lg border border-gray-200 shadow-sm">
            @else
                <div class="w-full aspect-square bg-gray-100 flex items-center justify-center rounded-lg border border-gray-200">
                    <span class="text-gray-400">Tidak ada gambar</span>
                </div>
            @endif
            
            <div class="mt-4 w-full">
                @if($produk->is_active)
                    <span class="w-full block text-center py-2 bg-green-100 text-green-800 rounded-md font-semibold">Status: Aktif</span>
                @else
                    <span class="w-full block text-center py-2 bg-red-100 text-red-800 rounded-md font-semibold">Status: Nonaktif</span>
                @endif
            </div>
        </div>

        <!-- Kolom Detail -->
        <div class="w-full md:w-2/3">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $produk->nama_produk }}</h2>
            <p class="text-sm text-gray-500 mb-6">SKU: <span class="font-mono text-gray-800">{{ $produk->sku ?? '-' }}</span></p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                
                <div class="border-b border-gray-100 pb-2">
                    <span class="block text-sm text-gray-500">Kategori</span>
                    <span class="block font-medium text-gray-900">{{ $produk->kategori->nama ?? '-' }}</span>
                </div>
                
                <div class="border-b border-gray-100 pb-2">
                    <span class="block text-sm text-gray-500">Gudang</span>
                    <span class="block font-medium text-gray-900">{{ $produk->gudang->nama ?? '-' }}</span>
                </div>

                <div class="border-b border-gray-100 pb-2">
                    <span class="block text-sm text-gray-500">Merk / Brand</span>
                    <span class="block font-medium text-gray-900">{{ $produk->merk ?? '-' }}</span>
                </div>

                <div class="border-b border-gray-100 pb-2">
                    <span class="block text-sm text-gray-500">Gender</span>
                    <span class="block font-medium text-gray-900">{{ $produk->gender ?? '-' }}</span>
                </div>

                <div class="border-b border-gray-100 pb-2">
                    <span class="block text-sm text-gray-500">Warna</span>
                    <span class="block font-medium text-gray-900">{{ $produk->warna ?? '-' }}</span>
                </div>

                <div class="border-b border-gray-100 pb-2">
                    <span class="block text-sm text-gray-500">Ukuran</span>
                    <span class="block font-medium text-gray-900">{{ $produk->ukuran ?? '-' }}</span>
                </div>

            </div>

            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Informasi Harga & Stok</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="block text-sm text-gray-500">Harga Beli</span>
                        <span class="block font-medium text-gray-900">Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Harga Jual</span>
                        <span class="block font-medium text-green-600 text-lg">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Margin Profit</span>
                        <span class="block font-medium text-blue-600">Rp {{ number_format($produk->harga_jual - $produk->harga_beli, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Stok Tersedia</span>
                        <span class="block font-medium {{ $produk->stok <= $produk->stok_minimum ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $produk->stok }} unit
                            @if($produk->stok <= $produk->stok_minimum)
                                <span class="text-xs text-red-500 ml-1">(Minimum: {{ $produk->stok_minimum }})</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            @if($produk->deskripsi)
                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Deskripsi</h3>
                    <div class="prose prose-sm text-gray-600 max-w-none">
                        {{ $produk->deskripsi }}
                    </div>
                </div>
            @endif

            <div class="mt-8 flex gap-3">
                <a href="{{ route('produk.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Kembali
                </a>
                <a href="{{ route('produk.edit', $produk->id) }}" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Edit Produk
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
