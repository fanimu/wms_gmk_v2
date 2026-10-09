@extends('layouts.app')

@section('page-title', 'Transaksi Penjualan')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-100 min-h-screen">
    <!-- Header Tabs -->
    <div class="border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">
            <li class="mr-2">
                <a href="#" class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50">Pantauan</a>
            </li>
            <li class="mr-2">
                <a href="#" class="inline-block p-4 text-indigo-600 border-b-2 border-indigo-600 rounded-t-lg active">Pesanan</a>
            </li>
            <li class="mr-2">
                <a href="#" class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50">Faktur</a>
            </li>
            <li class="mr-2">
                <a href="#" class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50">Retur</a>
            </li>
        </ul>
    </div>

    <!-- Main Content Area: Sidebar + Table -->
    <div class="flex flex-col md:flex-row">
        
        <!-- Left Sidebar Filters -->
        <div class="w-full md:w-64 flex-shrink-0 border-r border-gray-200 p-4 space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Filter</h3>
                <a href="{{ route('order.index') }}" class="text-sm text-red-500 hover:text-red-700">Reset</a>
            </div>

            <form method="GET" action="{{ route('order.index') }}" class="space-y-4" id="filterForm">
                
                <!-- Cari Berdasarkan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Berdasarkan</label>
                    <div class="flex items-center space-x-4 mb-2">
                        <label class="flex items-center">
                            <input type="radio" name="search_by" value="pesanan" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500" checked>
                            <span class="ml-2 text-sm text-gray-600">Pesanan</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="search_by" value="produk" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600">Produk</span>
                        </label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2" placeholder="Cari Pesanan">
                    </div>
                </div>

                <!-- Dropdowns -->
                <div>
                    <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2">
                        <option value="">Pilih Lokasi</option>
                        <option value="pusat">Pusat</option>
                    </select>
                </div>
                
                <div>
                    <select name="status" onchange="document.getElementById('filterForm').submit()" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2">
                        <option value="semua">Cari Status Channel</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses (Processing)</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Dikirim (Shipped)</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Selesai (Delivered)</option>
                    </select>
                </div>

                <div>
                    <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2">
                        <option value="">Cari Kurir</option>
                    </select>
                </div>

                <div>
                    <select name="platform" onchange="document.getElementById('filterForm').submit()" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2">
                        <option value="semua">Pilih Channel</option>
                        <option value="shopee" {{ request('platform') == 'shopee' ? 'selected' : '' }}>Shopee</option>
                        <option value="tiktok" {{ request('platform') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                    </select>
                </div>
                
                <div>
                    <select name="sort" onchange="document.getElementById('filterForm').submit()" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2">
                        <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru ke Terlama</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama ke Terbaru</option>
                    </select>
                </div>

            </form>
            
            <!-- Tambahan Tombol Tarik Pesanan (Opsional tapi berguna di Sidebar) -->
            <form method="POST" action="{{ route('order.fetch') }}" class="pt-4 border-t border-gray-200">
                @csrf
                <button type="submit" class="w-full text-white bg-indigo-600 hover:bg-indigo-700 font-medium rounded-md text-sm px-4 py-2">
                    Tarik Pesanan Terbaru
                </button>
            </form>
        </div>

        <!-- Right Content Area (Status Chips & Table) -->
        <div class="flex-1 p-4 overflow-hidden">
            
            <!-- Status Chips & Top Bar -->
            <div class="flex justify-between items-center mb-4 overflow-x-auto pb-2">
                <div class="flex space-x-2">
                    <a href="{{ route('order.index', ['status' => 'semua']) }}" class="px-4 py-1.5 text-sm rounded-full border {{ request('status', 'semua') == 'semua' ? 'border-indigo-600 text-indigo-600 bg-indigo-50' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">Semua</a>
                    <a href="{{ route('order.index', ['status' => 'pending']) }}" class="px-4 py-1.5 text-sm rounded-full border {{ request('status') == 'pending' ? 'border-indigo-600 text-indigo-600 bg-indigo-50' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">Belum Dibayar</a>
                    <a href="{{ route('order.index', ['status' => 'processing']) }}" class="px-4 py-1.5 text-sm rounded-full border flex items-center {{ request('status') == 'processing' ? 'border-indigo-600 text-indigo-600 bg-indigo-50' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                        Siap Proses
                        @if($orders->total() > 0 && request('status') == 'processing')
                            <span class="ml-2 bg-indigo-600 text-white text-xs px-2 py-0.5 rounded-full">{{ $orders->total() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('order.index', ['status' => 'cancelled']) }}" class="px-4 py-1.5 text-sm rounded-full border flex items-center {{ request('status') == 'cancelled' ? 'border-red-600 text-red-600 bg-red-50' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                        Request Batal
                    </a>
                    <button class="px-3 py-1.5 text-sm rounded-full border border-gray-300 text-gray-600 hover:bg-gray-50">...</button>
                </div>
                
                <div class="flex items-center space-x-4 text-sm text-gray-600 whitespace-nowrap ml-4">
                    <a href="{{ url()->full() }}" class="hover:text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </a>
                    <span class="font-medium bg-gray-100 px-3 py-1 rounded">Total {{ $orders->total() }}</span>
                </div>
            </div>

            <!-- The Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-xs text-left text-gray-600">
                    <thead class="text-xs text-gray-700 bg-gray-50 border-b border-gray-200 whitespace-nowrap">
                        <tr>
                            <th scope="col" class="p-4">
                                <input type="checkbox" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500">
                            </th>
                            <th scope="col" class="px-4 py-3 font-semibold">No. Pesanan</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Tanggal Pesanan</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Penerima</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Lokasi</th>
                            <th scope="col" class="px-4 py-3 font-semibold text-center">Total SKU/Qty</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Nilai</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Toko</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Kurir</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Batas Kirim</th>
                            <th scope="col" class="px-4 py-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($orders as $order)
                            @php
                                // Masking name logic
                                $name = $order->buyer_name ?? 'Unknown';
                                $maskedName = $name;
                                if(strlen($name) > 2) {
                                    $first = substr($name, 0, 1);
                                    $last = substr($name, -1);
                                    $maskedName = $first . '***' . $last;
                                }

                                // Date formatting
                                $dt = \Carbon\Carbon::parse($order->order_date);
                                $dateStr = $dt->translatedFormat('d M Y');
                                $timeStr = $dt->format('H:i');
                                
                                // Qty Calc
                                $totalQty = $order->items->sum('quantity') ?: 1;
                                
                                // Fake batas kirim logic for visual
                                $batasKirim = \Carbon\Carbon::parse($order->order_date)->addDays(2);
                                $diff = now()->diff($batasKirim);
                                $batasStr = $batasKirim->isPast() ? 'Terlambat' : $diff->d . ' Hari ' . $diff->h . ' Jam ' . $diff->i . ' Menit';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 w-4">
                                    <input type="checkbox" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500">
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('order.show', $order->id) }}" class="font-medium text-blue-600 hover:underline">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $dateStr }}<br>
                                    <span class="text-gray-400">{{ $timeStr }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $maskedName }}
                                </td>
                                <td class="px-4 py-3">
                                    Pusat
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ $totalQty }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-800">
                                    Rp. {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap flex items-center space-x-2">
                                    @if(strtolower($order->platform) == 'shopee')
                                        <span class="w-5 h-5 rounded bg-orange-100 text-orange-600 flex items-center justify-center text-[10px] font-bold">S</span>
                                    @elseif(strtolower($order->platform) == 'tiktok')
                                        <span class="w-5 h-5 rounded bg-black text-white flex items-center justify-center text-[10px] font-bold">TT</span>
                                    @else
                                        <span class="w-5 h-5 rounded bg-gray-200 text-gray-600 flex items-center justify-center text-[10px] font-bold">M</span>
                                    @endif
                                    <span class="truncate w-24">Toko WMS</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $order->shipping_provider ?? 'Kurir Standar' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-red-600">
                                    {{ $batasStr }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('order.show', $order->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded border border-gray-300 hover:bg-gray-50 text-gray-600">
                                        ...
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-8 text-center text-gray-500">
                                    Tidak ada pesanan yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
