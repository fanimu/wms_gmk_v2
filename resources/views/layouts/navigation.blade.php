<!-- Sidebar Background Overlay -->
<div x-show="sidebarOpen" 
     class="fixed inset-0 z-20 bg-black bg-opacity-50 transition-opacity lg:hidden"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"></div>

<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 z-30 w-[260px] bg-slate-800 text-slate-300 transition-transform duration-300 transform lg:translate-x-0 lg:static lg:inset-0 flex flex-col"
       :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
    
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 bg-slate-900 shadow-md px-4 shrink-0">
        <a href="{{ route('dashboard') ?? '#' }}" class="flex flex-col items-center">
            <span class="text-white text-xl font-bold tracking-wider">WMS GMK</span>
            <span class="text-slate-400 text-xs tracking-widest uppercase">PT Giat Mitra Karya</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-3 py-6 overflow-y-auto space-y-6">
        
        <!-- Dashboard -->
        <div>
            <a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}" 
               class="flex items-center px-3 py-2.5 rounded-lg transition-colors group
               {{ request()->routeIs('dashboard') ? 'bg-indigo-600/10 text-indigo-400 border-l-4 border-indigo-500' : 'hover:bg-slate-700/50 hover:text-white border-l-4 border-transparent' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span class="font-medium text-sm">Dashboard</span>
            </a>
        </div>

        <!-- Master Data -->
        <div>
            <h3 class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Master Data</h3>
            <div class="space-y-1">
                <a href="{{ Route::has('gudang.index') ? route('gudang.index') : '#' }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg transition-colors group
                   {{ request()->routeIs('gudang.*') ? 'bg-indigo-600/10 text-indigo-400 border-l-4 border-indigo-500' : 'hover:bg-slate-700/50 hover:text-white border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="font-medium text-sm">Data Gudang</span>
                </a>

                <a href="{{ Route::has('supplier.index') ? route('supplier.index') : '#' }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg transition-colors group
                   {{ request()->routeIs('supplier.*') ? 'bg-indigo-600/10 text-indigo-400 border-l-4 border-indigo-500' : 'hover:bg-slate-700/50 hover:text-white border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span class="font-medium text-sm">Data Supplier</span>
                </a>

                <a href="{{ Route::has('produk.index') ? route('produk.index') : '#' }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg transition-colors group
                   {{ request()->routeIs('produk.*') ? 'bg-indigo-600/10 text-indigo-400 border-l-4 border-indigo-500' : 'hover:bg-slate-700/50 hover:text-white border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span class="font-medium text-sm">Master Produk</span>
                </a>

                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg transition-colors group opacity-50 cursor-not-allowed hover:bg-transparent border-l-4 border-transparent">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span class="font-medium text-sm">Kategori</span>
                    <span class="ml-auto text-[10px] bg-slate-700 text-slate-300 py-0.5 px-1.5 rounded">(Segera)</span>
                </a>
            </div>
        </div>

        <!-- Transaksi -->
        <div>
            <h3 class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Transaksi</h3>
            <div class="space-y-1">
                <a href="{{ Route::has('transaksi.index') ? route('transaksi.index') : '#' }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg transition-colors group
                   {{ request()->routeIs('transaksi.*') ? 'bg-indigo-600/10 text-indigo-400 border-l-4 border-indigo-500' : 'hover:bg-slate-700/50 hover:text-white border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span class="font-medium text-sm">Transaksi Barang</span>
                </a>
            </div>
        </div>

        <!-- Marketplace -->
        <div>
            <h3 class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Marketplace</h3>
            <div class="space-y-1">
                <a href="{{ Route::has('marketplace.index') ? route('marketplace.index') : '#' }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg transition-colors group
                   {{ request()->routeIs('marketplace.*') ? 'bg-indigo-600/10 text-indigo-400 border-l-4 border-indigo-500' : 'hover:bg-slate-700/50 hover:text-white border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="font-medium text-sm">Marketplace</span>
                </a>
                
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg transition-colors group opacity-50 cursor-not-allowed hover:bg-transparent border-l-4 border-transparent">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <span class="font-medium text-sm">Orders</span>
                    <span class="ml-auto text-[10px] bg-slate-700 text-slate-300 py-0.5 px-1.5 rounded">(Segera)</span>
                </a>
            </div>
        </div>

        <!-- Laporan -->
        <div>
            <h3 class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Laporan</h3>
            <div class="space-y-1">
                <a href="{{ Route::has('laporan.stok') ? route('laporan.stok') : '#' }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg transition-colors group
                   {{ request()->routeIs('laporan.stok') ? 'bg-indigo-600/10 text-indigo-400 border-l-4 border-indigo-500' : 'hover:bg-slate-700/50 hover:text-white border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="font-medium text-sm">Laporan Stok</span>
                </a>

                <a href="{{ Route::has('laporan.transaksi') ? route('laporan.transaksi') : '#' }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg transition-colors group
                   {{ request()->routeIs('laporan.transaksi') ? 'bg-indigo-600/10 text-indigo-400 border-l-4 border-indigo-500' : 'hover:bg-slate-700/50 hover:text-white border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium text-sm">Laporan Transaksi</span>
                </a>

                <a href="{{ Route::has('laporan.penjualan') ? route('laporan.penjualan') : '#' }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg transition-colors group
                   {{ request()->routeIs('laporan.penjualan') ? 'bg-indigo-600/10 text-indigo-400 border-l-4 border-indigo-500' : 'hover:bg-slate-700/50 hover:text-white border-l-4 border-transparent' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium text-sm">Laporan Penjualan</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Footer Sidebar -->
    <div class="px-4 py-4 bg-slate-900/50 shrink-0 border-t border-slate-700/50 text-center">
        <p class="text-[11px] text-slate-500 font-medium">v2.0 | Omnichannel Ready</p>
    </div>
</aside>
