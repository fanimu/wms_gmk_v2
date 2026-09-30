@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Greeting -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-800">Selamat datang, {{ Auth::user()->name ?? 'User' }}!</h2>
        <p class="text-gray-500 mt-1">Berikut adalah ringkasan aktivitas warehouse hari ini.</p>
    </div>

    <!-- Stat Cards -->
    @php
        // Dummy default stats if not passed from controller
        $stats = $stats ?? [
            'total_produk' => 1250,
            'total_gudang' => 4,
            'total_supplier' => 32,
            'stok_rendah' => 15,
            'transaksi_hari_ini' => 48,
            'order_pending' => 7
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Total Produk -->
        <x-stat-card 
            color="blue" 
            label="Total Produk Aktif" 
            :value="$stats['total_produk']" 
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>' 
        />
        
        <!-- Total Gudang -->
        <x-stat-card 
            color="green" 
            label="Total Gudang" 
            :value="$stats['total_gudang']" 
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>' 
        />
        
        <!-- Total Supplier -->
        <x-stat-card 
            color="purple" 
            label="Total Supplier" 
            :value="$stats['total_supplier']" 
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>' 
        />
        
        <!-- Stok Rendah -->
        <x-stat-card 
            :color="$stats['stok_rendah'] > 0 ? 'red' : 'green'" 
            label="Stok Rendah" 
            :value="$stats['stok_rendah']" 
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>' 
        />
        
        <!-- Transaksi Hari Ini -->
        <x-stat-card 
            color="cyan" 
            label="Transaksi Hari Ini" 
            :value="$stats['transaksi_hari_ini']" 
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>' 
        />
        
        <!-- Order Pending -->
        <x-stat-card 
            color="orange" 
            label="Order Pending" 
            :value="$stats['order_pending']" 
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' 
        />
    </div>

    <!-- Tables Section -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        
        <!-- Transaksi Terbaru -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Transaksi Terbaru</h3>
                <a href="{{ Route::has('transaksi.index') ? route('transaksi.index') : '#' }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 bg-gray-50 uppercase">
                        <tr>
                            <th class="px-6 py-3 font-medium">Kode</th>
                            <th class="px-6 py-3 font-medium">Jenis</th>
                            <th class="px-6 py-3 font-medium">Gudang</th>
                            <th class="px-6 py-3 font-medium">Tanggal</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            // Dummy transactions
                            $transactions = [
                                ['kode' => 'TRX-001', 'jenis' => 'MASUK', 'gudang' => 'Gudang Utama', 'tanggal' => '2023-10-25', 'status' => 'CONFIRMED'],
                                ['kode' => 'TRX-002', 'jenis' => 'KELUAR', 'gudang' => 'Gudang Transit', 'tanggal' => '2023-10-25', 'status' => 'DRAFT'],
                                ['kode' => 'TRX-003', 'jenis' => 'RETUR', 'gudang' => 'Gudang Utama', 'tanggal' => '2023-10-24', 'status' => 'CONFIRMED'],
                                ['kode' => 'TRX-004', 'jenis' => 'ADJUSTMENT', 'gudang' => 'Gudang Rusak', 'tanggal' => '2023-10-24', 'status' => 'CANCELLED'],
                            ];
                        @endphp
                        
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $trx['kode'] }}</td>
                            <td class="px-6 py-3">
                                @php
                                    $jenisType = match($trx['jenis']) {
                                        'MASUK' => 'success',
                                        'KELUAR' => 'danger',
                                        'RETUR' => 'warning',
                                        'ADJUSTMENT' => 'info',
                                        default => 'default'
                                    };
                                @endphp
                                <x-badge :type="$jenisType">{{ $trx['jenis'] }}</x-badge>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $trx['gudang'] }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $trx['tanggal'] }}</td>
                            <td class="px-6 py-3">
                                @php
                                    $statusType = match($trx['status']) {
                                        'CONFIRMED' => 'success',
                                        'DRAFT' => 'default',
                                        'CANCELLED' => 'danger',
                                        default => 'default'
                                    };
                                @endphp
                                <x-badge :type="$statusType">{{ $trx['status'] }}</x-badge>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada transaksi terbaru</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Produk Stok Rendah -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Produk Stok Rendah</h3>
                <a href="{{ Route::has('laporan.stok') ? route('laporan.stok') : '#' }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Laporan Stok &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 bg-gray-50 uppercase">
                        <tr>
                            <th class="px-6 py-3 font-medium">Produk</th>
                            <th class="px-6 py-3 font-medium">SKU</th>
                            <th class="px-6 py-3 font-medium text-center">Stok / Min</th>
                            <th class="px-6 py-3 font-medium">Gudang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            // Dummy low stock products
                            $lowStocks = [
                                ['nama' => 'Kaos Polos Hitam M', 'sku' => 'KPH-M-01', 'stok' => 5, 'min' => 20, 'gudang' => 'Gudang Utama'],
                                ['nama' => 'Kemeja Flanel L', 'sku' => 'KFL-L-02', 'stok' => 2, 'min' => 15, 'gudang' => 'Gudang Utama'],
                                ['nama' => 'Celana Chino 32', 'sku' => 'CLN-32-05', 'stok' => 0, 'min' => 10, 'gudang' => 'Gudang Transit'],
                                ['nama' => 'Topi Baseball Hitam', 'sku' => 'TPB-HTM', 'stok' => 8, 'min' => 25, 'gudang' => 'Gudang Aksesoris'],
                            ];
                        @endphp
                        
                        @forelse($lowStocks as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $item['nama'] }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $item['sku'] }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="font-bold text-red-600">{{ $item['stok'] }}</span>
                                <span class="text-gray-400 mx-1">/</span>
                                <span class="text-gray-500">{{ $item['min'] }}</span>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $item['gudang'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Semua produk dalam kondisi stok aman</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
