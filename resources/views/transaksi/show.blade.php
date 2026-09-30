@extends('layouts.app')

@section('page-title', 'Detail Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item text-sm"><a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline">Dashboard</a></li>
    <li class="breadcrumb-item text-sm"><a href="{{ route('transaksi.index') }}" class="text-blue-500 hover:underline">/ Transaksi</a></li>
    <li class="breadcrumb-item text-sm text-gray-500 active" aria-current="page">/ Detail</li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        
        <!-- Header Info -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $transaksi->kode }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Dibuat oleh: {{ $transaksi->creator ? $transaksi->creator->name : 'Sistem' }} pada {{ $transaksi->created_at->format('d M Y H:i') }}</p>
                </div>
                <div class="mt-4 md:mt-0 flex gap-2">
                    @if($transaksi->status == 'DRAFT')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">DRAFT</span>
                    @elseif($transaksi->status == 'CONFIRMED')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">CONFIRMED</span>
                    @elseif($transaksi->status == 'CANCELLED')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">CANCELLED</span>
                    @endif
                    
                    @if($transaksi->jenis == 'MASUK')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">MASUK</span>
                    @elseif($transaksi->jenis == 'KELUAR')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">KELUAR</span>
                    @elseif($transaksi->jenis == 'RETUR')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">RETUR</span>
                    @elseif($transaksi->jenis == 'ADJUSTMENT')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200">ADJUSTMENT</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tanggal Transaksi</p>
                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Gudang</p>
                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $transaksi->gudang ? $transaksi->gudang->nama : '-' }}</p>
                </div>
                @if($transaksi->jenis == 'MASUK')
                <div>
                    <p class="text-sm font-medium text-gray-500">Supplier</p>
                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $transaksi->supplier ? $transaksi->supplier->nama : '-' }}</p>
                </div>
                @endif
                <div class="lg:col-span-2">
                    <p class="text-sm font-medium text-gray-500">Keterangan</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $transaksi->keterangan ?: '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Detail Items -->
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Barang</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $totalQty = 0;
                            $totalNilai = 0;
                        @endphp
                        @forelse($transaksi->details ?? [] as $index => $detail)
                        @php
                            $subtotal = $detail->qty * $detail->harga_satuan;
                            $totalQty += $detail->qty;
                            $totalNilai += $subtotal;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $detail->produk ? $detail->produk->kode . ' - ' . $detail->produk->nama : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($detail->qty, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $detail->catatan ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada detail barang.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-right text-sm text-gray-900">Total:</td>
                            <td class="px-6 py-4 text-right text-sm text-gray-900">{{ number_format($totalQty, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-sm text-gray-900"></td>
                            <td class="px-6 py-4 text-right text-sm text-gray-900">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200">
            <a href="{{ route('transaksi.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded shadow transition duration-150 ease-in-out">
                Kembali
            </a>
            
            @if($transaksi->status == 'DRAFT')
            <div class="flex gap-3">
                <a href="{{ route('transaksi.edit', $transaksi->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150 ease-in-out">
                    Edit
                </a>
                
                <form action="{{ route('transaksi.cancel', $transaksi->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi ini? Transaksi yang dibatalkan tidak dapat diubah lagi.');">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150 ease-in-out">
                        Batalkan
                    </button>
                </form>

                <form action="{{ route('transaksi.confirm', $transaksi->id) }}" method="POST" onsubmit="return confirm('Konfirmasi transaksi ini? Stok barang akan diperbarui dan status menjadi CONFIRMED.');">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150 ease-in-out">
                        Konfirmasi Transaksi
                    </button>
                </form>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
