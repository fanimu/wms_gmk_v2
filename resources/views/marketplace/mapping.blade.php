@extends('layouts.app')

@section('title', 'Mapping Produk')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Mapping Produk Marketplace
            </h2>
            <p class="text-sm text-slate-500 mt-1">Kelola tautan produk WMS dengan produk di Shopee dan TikTok.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
        <div class="p-6 bg-white border-b border-slate-200">
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                SKU & WMS Produk
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Tautan Shopee
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Tautan TikTok
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($produks as $produk)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-slate-900">
                                                {{ $produk->nama }}
                                            </div>
                                            <div class="text-sm text-slate-500">
                                                SKU: {{ $produk->sku }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                {{-- Shopee Link Column --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $shopeeLinks = $produk->marketplaceProducts->where('store.platform', 'shopee');
                                    @endphp
                                    
                                    @if($shopeeLinks->count() > 0)
                                        <div class="flex flex-col items-center space-y-1">
                                            @foreach($shopeeLinks as $link)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    ID: {{ $link->platform_product_id }} - Rp {{ number_format($link->platform_price, 0, ',', '.') }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                            Belum Terhubung
                                        </span>
                                    @endif
                                </td>

                                {{-- TikTok Link Column --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $tiktokLinks = $produk->marketplaceProducts->where('store.platform', 'tiktok');
                                    @endphp
                                    
                                    @if($tiktokLinks->count() > 0)
                                        <div class="flex flex-col items-center space-y-1">
                                            @foreach($tiktokLinks as $link)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    ID: {{ $link->platform_product_id }} - Rp {{ number_format($link->platform_price, 0, ',', '.') }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                            Belum Terhubung
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button type="button" onclick="alert('Fitur Tautkan Manual belum tersedia')" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-md transition-colors">
                                        Tautkan Manual
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 text-center">
                                    Tidak ada produk yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                @if(isset($produks) && method_exists($produks, 'links'))
                    {{ $produks->links() }}
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
