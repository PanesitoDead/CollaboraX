<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CollaboraX') }} - @yield('title', 'Coordinador de Equipo')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex h-screen">
        {{-- Sidebar --}}
        {{-- @include('components.coord-equipo-sidebar') --}}

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Header --}}
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 class="ml-4 text-2xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    {{-- User Menu --}}
                    <div class="flex items-center space-x-4">
                        {{-- Notifications --}}
                        <button class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6a2 2 0 012 2v9a2 2 0 01-2 2H9l-5-5V9a2 2 0 012-2z" />
                            </svg>
                        </button>

                        {{-- User Dropdown --}}
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <img class="w-8 h-8 rounded-full" src="{{ asset('placeholder-40x40.png') }}" alt="Avatar">
                                <span class="hidden md:block text-gray-700 font-medium">@yield('user-name', 'Coordinador')</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('coord-equipo.configuracion') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Configuración</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cerrar Sesión</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            <main class="flex-1 overflow-y-auto p-4">
                @if(session('success'))
                    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Base Scripts --}}
    <script>
        document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });
        document.getElementById('user-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        });
        document.addEventListener('click', function(event) {
            const btn = document.getElementById('user-menu-button');
            const menu = document.getElementById('user-menu');
            if (btn && menu && !btn.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>