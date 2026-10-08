@extends('layouts.app')

@section('page-title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pesanan #{{ $order->order_number }}</h2>
            <p class="text-sm text-gray-500">Tanggal: {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($order->status == 'pending')
                <form action="{{ route('order.status', $order->id) }}" method="POST" class="inline-block">
                    @csrf
                    <input type="hidden" name="status" value="processing">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition">
                        Tandai Diproses
                    </button>
                </form>
            @endif

            @if($order->status == 'processing')
                <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition">
                    Cetak Resi
                </button>
                <form action="{{ route('order.status', $order->id) }}" method="POST" class="inline-block">
                    @csrf
                    <input type="hidden" name="status" value="shipped">
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition">
                        Tandai Dikirim
                    </button>
                </form>
            @endif
            
            <a href="{{ route('order.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg text-sm transition">
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Info Pesanan & Pengiriman -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Informasi Pesanan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Informasi Pesanan</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Platform</span>
                        <span class="font-medium text-gray-900">{{ ucfirst($order->platform) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        @php
                            $badgeClass = 'bg-gray-100 text-gray-800';
                            $statusText = ucfirst($order->status);
                            
                            if ($order->status == 'pending') {
                                $badgeClass = 'bg-yellow-100 text-yellow-800';
                                $statusText = 'Menunggu';
                            } elseif ($order->status == 'processing') {
                                $badgeClass = 'bg-blue-100 text-blue-800';
                                $statusText = 'Diproses';
                            } elseif ($order->status == 'shipped') {
                                $badgeClass = 'bg-purple-100 text-purple-800';
                                $statusText = 'Dikirim';
                            } elseif ($order->status == 'delivered') {
                                $badgeClass = 'bg-green-100 text-green-800';
                                $statusText = 'Selesai';
                            } elseif ($order->status == 'cancelled') {
                                $badgeClass = 'bg-red-100 text-red-800';
                                $statusText = 'Batal';
                            }
                        @endphp
                        <span class="text-xs font-medium px-2.5 py-0.5 rounded {{ $badgeClass }}">{{ $statusText }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Catatan</span>
                        <span class="font-medium text-gray-900 text-right">{{ $order->notes ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Rincian Pelanggan & Pengiriman -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Rincian Pelanggan & Pengiriman</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <span class="block text-gray-500 mb-1">Nama Pembeli</span>
                        <span class="font-medium text-gray-900">{{ $order->customer_name }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 mb-1">Telepon</span>
                        <span class="font-medium text-gray-900">{{ $order->customer_phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 mb-1">Alamat Pengiriman</span>
                        <span class="font-medium text-gray-900 block leading-relaxed">{{ $order->shipping_address ?? '-' }}</span>
                    </div>
                    <div class="pt-2 border-t border-gray-50">
                        <span class="block text-gray-500 mb-1">Kurir</span>
                        <span class="font-medium text-gray-900">{{ $order->shipping_carrier ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 mb-1">No. Resi</span>
                        <span class="font-medium text-gray-900">{{ $order->tracking_number ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Produk -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Produk</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 bg-gray-50 uppercase">
                            <tr>
                                <th class="px-6 py-3 font-medium">Produk</th>
                                <th class="px-6 py-3 font-medium text-center">Qty</th>
                                <th class="px-6 py-3 font-medium text-right">Harga</th>
                                <th class="px-6 py-3 font-medium text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                                        <div class="text-xs text-gray-500 mt-1">SKU: {{ $item->sku }}</div>
                                        @if($item->produk)
                                            <div class="text-xs text-indigo-600 mt-1">Terkait: {{ $item->produk->nama_produk }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-gray-900">
                                        Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 text-sm">
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-700">Total Keseluruhan</td>
                                <td class="px-6 py-3 text-right font-bold text-gray-900 text-base">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
