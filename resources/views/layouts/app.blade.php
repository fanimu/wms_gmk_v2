<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WMS GMK') }} - @yield('page-title', 'Dashboard')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false, profileOpen: false }">
        
        <!-- Sidebar Navigation -->
        @include('layouts.navigation')

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            
            <!-- Top Header -->
            <header class="bg-white shadow-sm z-10 shrink-0 border-b border-gray-200">
                <div class="flex items-center justify-between px-4 sm:px-6 py-4">
                    
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 lg:hidden mr-4">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <div>
                            <h1 class="text-xl font-semibold text-gray-800">
                                @yield('page-title', 'Dashboard')
                            </h1>
                            <div class="text-sm text-gray-500 mt-0.5">
                                @yield('breadcrumb')
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Profile dropdown -->
                        <div class="relative">
                            <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="flex items-center space-x-2 focus:outline-none">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border border-indigo-200">
                                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="hidden md:block text-sm font-medium text-gray-700">{{ Auth::user()->name ?? 'User' }}</span>
                                <svg class="hidden md:block w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown menu -->
                            <div x-show="profileOpen" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50"
                                 style="display: none;">
                                
                                <a href="{{ Route::has('profile.edit') ? route('profile.edit') : '#' }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Profil Saya
                                </a>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-50">
                <x-alert />
                
                @yield('content')
                
                @if(isset($slot))
                    {{ $slot }}
                @endif
            </main>
        </div>
        
        @livewireScripts
    </body>
</html>
