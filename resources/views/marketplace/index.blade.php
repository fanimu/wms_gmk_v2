<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Integrasi Marketplace') }}
            </h2>
            <div class="text-sm text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span class="mx-2">/</span>
                <span class="text-gray-700">Marketplace</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header Section -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-start">
                    <div class="flex-shrink-0 bg-indigo-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Hubungkan Toko Anda</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Hubungkan toko Anda dengan marketplace untuk mengaktifkan sinkronisasi stok dan pesanan secara otomatis. 
                            Ini akan memudahkan Anda mengelola inventaris dari satu tempat.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Grid of Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                @php
                    $shopeeStore = $stores->where('platform', 'shopee')->first();
                    $tiktokStore = $stores->where('platform', 'tiktok')->first();
                @endphp

                <!-- Shopee Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-gray-50 bg-gradient-to-r from-orange-50 to-white flex-grow">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-[#ee4d2d] rounded-lg flex items-center justify-center flex-shrink-0 text-white font-bold text-xl">
                                    S
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900">Shopee</h4>
                                    <p class="text-xs text-gray-500">Marketplace Integration</p>
                                </div>
                            </div>
                            
                            @if($shopeeStore)
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full border border-green-200">
                                    Terhubung
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full border border-gray-200">
                                    Belum Terhubung
                                </span>
                            @endif
                        </div>

                        <div class="mt-6">
                            @if($shopeeStore)
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between border-b border-gray-100 pb-2">
                                        <span class="text-gray-500">Nama Toko:</span>
                                        <span class="font-medium text-gray-900">{{ $shopeeStore->shop_name ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-100 pb-2">
                                        <span class="text-gray-500">Shop ID:</span>
                                        <span class="font-medium text-gray-900">{{ $shopeeStore->shop_id }}</span>
                                    </div>
                                    <div class="flex justify-between pb-2">
                                        <span class="text-gray-500">Sync Terakhir:</span>
                                        <span class="font-medium text-gray-900">{{ $shopeeStore->last_sync_at ? $shopeeStore->last_sync_at->format('d M Y, H:i') : 'Belum pernah' }}</span>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 text-center py-4">
                                    Tautkan akun Shopee Seller Center Anda untuk mulai menyinkronkan stok dan pesanan secara otomatis.
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 flex justify-end gap-3 mt-auto">
                        @if($shopeeStore)
                            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Sync Manual
                            </button>
                            <button class="px-4 py-2 bg-red-50 border border-red-200 rounded-lg text-sm font-medium text-red-600 hover:bg-red-100 transition-colors">
                                Putuskan Koneksi
                            </button>
                        @else
                            <a href="{{ route('marketplace.shopee.connect') }}" class="w-full text-center px-4 py-2 bg-[#ee4d2d] rounded-lg text-sm font-medium text-white hover:bg-[#d73f21] transition-colors">
                                Hubungkan Shopee
                            </a>
                        @endif
                    </div>
                </div>

                <!-- TikTok Shop Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-gray-50 bg-gradient-to-r from-gray-100 to-white flex-grow">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.01.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.12-3.44-3.17-3.41-5.46.03-2.01 1.14-3.9 2.87-4.82 1.48-.82 3.25-1.02 4.9-.53.11.03.22.06.33.1.01.01.02.01.02.01v4.06c-1.03-.43-2.22-.44-3.23-.05-.98.39-1.74 1.25-1.93 2.27-.15.93.18 1.91.88 2.56.7.67 1.7.95 2.64.81.93-.13 1.77-.73 2.19-1.55.33-.61.47-1.31.45-2.01-.01-4.73 0-9.46 0-14.19-.01-.01-.02-.01-.02-.01z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900">TikTok Shop</h4>
                                    <p class="text-xs text-gray-500">Marketplace Integration</p>
                                </div>
                            </div>
                            
                            @if($tiktokStore)
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full border border-green-200">
                                    Terhubung
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full border border-gray-200">
                                    Belum Terhubung
                                </span>
                            @endif
                        </div>

                        <div class="mt-6">
                            @if($tiktokStore)
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between border-b border-gray-100 pb-2">
                                        <span class="text-gray-500">Nama Toko:</span>
                                        <span class="font-medium text-gray-900">{{ $tiktokStore->shop_name ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-100 pb-2">
                                        <span class="text-gray-500">Shop ID:</span>
                                        <span class="font-medium text-gray-900">{{ $tiktokStore->shop_id }}</span>
                                    </div>
                                    <div class="flex justify-between pb-2">
                                        <span class="text-gray-500">Sync Terakhir:</span>
                                        <span class="font-medium text-gray-900">{{ $tiktokStore->last_sync_at ? $tiktokStore->last_sync_at->format('d M Y, H:i') : 'Belum pernah' }}</span>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 text-center py-4">
                                    Integrasikan toko TikTok Shop Anda untuk mensinkronisasi data pesanan dan update stok secara real-time.
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 flex justify-end gap-3 mt-auto">
                        @if($tiktokStore)
                            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Sync Manual
                            </button>
                            <button class="px-4 py-2 bg-red-50 border border-red-200 rounded-lg text-sm font-medium text-red-600 hover:bg-red-100 transition-colors">
                                Putuskan Koneksi
                            </button>
                        @else
                            <a href="{{ route('marketplace.tiktok.connect') }}" class="w-full text-center px-4 py-2 bg-black rounded-lg text-sm font-medium text-white hover:bg-gray-800 transition-colors">
                                Hubungkan TikTok Shop
                            </a>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Table Daftar Toko Terhubung -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Toko Terhubung</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Platform</th>
                                <th class="px-6 py-3">Nama Toko</th>
                                <th class="px-6 py-3">Shop ID</th>
                                <th class="px-6 py-3">Status Token</th>
                                <th class="px-6 py-3">Terakhir Sync</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($stores as $store)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($store->platform == 'shopee')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-orange-100 text-orange-800">
                                                    Shopee
                                                </span>
                                            @elseif($store->platform == 'tiktok')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-gray-200 text-gray-800">
                                                    TikTok Shop
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    {{ ucfirst($store->platform) }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $store->shop_name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $store->shop_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($store->access_token)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Kadaluarsa
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $store->last_sync_at ? $store->last_sync_at->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</button>
                                        <button class="text-red-600 hover:text-red-900">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm">
                                        Belum ada toko yang terhubung. Silakan hubungkan toko Anda melalui kartu di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
