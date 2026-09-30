@extends('layouts.app')

@section('page-title', 'Transaksi Barang')

@section('breadcrumb')
    <li class="breadcrumb-item text-sm"><a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline">Dashboard</a></li>
    <li class="breadcrumb-item text-sm text-gray-500 active" aria-current="page">/ Transaksi</li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Transaksi</h1>
        <a href="{{ route('transaksi.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
            + Buat Transaksi
        </a>
    </div>

    <!-- Filter Section -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form action="{{ route('transaksi.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="w-full sm:w-auto">
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="w-full sm:w-auto">
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="w-full sm:w-auto">
                <label for="jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis Transaksi</label>
                <select name="jenis" id="jenis" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Jenis</option>
                    <option value="MASUK" {{ request('jenis') == 'MASUK' ? 'selected' : '' }}>Masuk</option>
                    <option value="KELUAR" {{ request('jenis') == 'KELUAR' ? 'selected' : '' }}>Keluar</option>
                    <option value="RETUR" {{ request('jenis') == 'RETUR' ? 'selected' : '' }}>Retur</option>
                    <option value="ADJUSTMENT" {{ request('jenis') == 'ADJUSTMENT' ? 'selected' : '' }}>Adjustment</option>
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="DRAFT" {{ request('status') == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                    <option value="CONFIRMED" {{ request('status') == 'CONFIRMED' ? 'selected' : '' }}>Confirmed</option>
                    <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="w-full sm:w-auto flex gap-2">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded shadow">Filter</button>
                <a href="{{ route('transaksi.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded shadow">Reset</a>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Transaksi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Item</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transaksis as $trx)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $trx->kode }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($trx->jenis == 'MASUK')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">MASUK</span>
                            @elseif($trx->jenis == 'KELUAR')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">KELUAR</span>
                            @elseif($trx->jenis == 'RETUR')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">RETUR</span>
                            @elseif($trx->jenis == 'ADJUSTMENT')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">ADJUSTMENT</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $trx->total_item ?? 0 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">{{ $trx->keterangan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($trx->status == 'DRAFT')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">DRAFT</span>
                            @elseif($trx->status == 'CONFIRMED')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">CONFIRMED</span>
                            @elseif($trx->status == 'CANCELLED')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">CANCELLED</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('transaksi.show', $trx->id) }}" class="text-blue-600 hover:text-blue-900" title="Detail">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @if($trx->status == 'DRAFT')
                                    <a href="{{ route('transaksi.edit', $trx->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('transaksi.destroy', $trx->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada data transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($transaksis) && $transaksis->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transaksis->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
