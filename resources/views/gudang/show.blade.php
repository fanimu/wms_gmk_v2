@extends('layouts.app')

@section('page-title', 'Detail Gudang')

@section('breadcrumb')
    <nav class="flex text-sm text-gray-500" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center hover:text-gray-900">
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('gudang.index') }}" class="ml-1 md:ml-2 hover:text-gray-900">Data Gudang</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 md:ml-2 text-gray-400">Detail</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Informasi Gudang</h3>
            <div class="flex space-x-2">
                <a href="{{ route('gudang.edit', $gudang) }}" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-4 py-2 text-center">
                    Edit
                </a>
                <a href="{{ route('gudang.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-4 py-2">
                    Kembali
                </a>
            </div>
        </div>
        
        <div class="border-t border-gray-200 py-5">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Kode Gudang</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $gudang->kode_gudang }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama Gudang</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $gudang->nama_gudang }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Lokasi</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $gudang->lokasi }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Penanggung Jawab</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $gudang->penanggung_jawab ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Telepon</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $gudang->telepon ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($gudang->is_active)
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded border border-green-400">Aktif</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded border border-red-400">Nonaktif</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dibuat pada</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $gudang->created_at->format('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Diupdate pada</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $gudang->updated_at->format('d M Y H:i') }}</dd>
                </div>
            </dl>
        </div>
        
        <div class="mt-6 border-t border-gray-200 pt-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Ringkasan</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-indigo-50 rounded-lg p-4 flex items-center justify-between border border-indigo-100">
                    <div>
                        <p class="text-sm text-indigo-600 font-medium">Jumlah Produk</p>
                        <p class="text-2xl font-bold text-indigo-900">{{ $jumlahProduk ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 rounded-full text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 flex items-center justify-between border border-blue-100">
                    <div>
                        <p class="text-sm text-blue-600 font-medium">Jumlah Transaksi</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $jumlahTransaksi ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
