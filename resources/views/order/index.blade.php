@extends('layouts.app')

@section('page-title', 'Pesanan Marketplace')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Pesanan Marketplace</h2>
        <form method="POST" action="{{ route('order.fetch') }}">
            @csrf
            <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                Tarik Pesanan Terbaru
            </button>
        </form>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('order.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses (Processing)</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Dikirim (Shipped)</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Selesai (Delivered)</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Batal (Cancelled)</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan (Returned)</option>
                </select>
            </div>
            <div>
                <label for="platform" class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
                <select name="platform" id="platform" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                    <option value="">Semua Platform</option>
                    <option value="shopee" {{ request('platform') == 'shopee' ? 'selected' : '' }}>Shopee</option>
                    <option value="tiktok" {{ request('platform') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                    <option value="manual" {{ request('platform') == 'manual' ? 'selected' : '' }}>Manual</option>
                </select>
            </div>
            <div>
                <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 bg-gray-50 uppercase">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">No. Pesanan</th>
                        <th scope="col" class="px-6 py-3 font-medium">Platform</th>
                        <th scope="col" class="px-6 py-3 font-medium">Tanggal</th>
                        <th scope="col" class="px-6 py-3 font-medium">Pembeli</th>
                        <th scope="col" class="px-6 py-3 font-medium">Total</th>
                        <th scope="col" class="px-6 py-3 font-medium">Status</th>
                        <th scope="col" class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4">
                                @if(strtolower($order->platform) == 'shopee')
                                    <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded">Shopee</span>
                                @elseif(strtolower($order->platform) == 'tiktok')
                                    <span class="bg-black text-white text-xs font-medium px-2.5 py-0.5 rounded">TikTok</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ ucfirst($order->platform) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $order->customer_name }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
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
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('order.show', $order->id) }}" class="font-medium text-indigo-600 hover:text-indigo-900">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                Tidak ada pesanan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
