{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CollaboraX') }} - @yield('title', 'Colaborador')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="font-sans antialiased">
    <div class="flex h-screen bg-gray-100">
        {{-- Sidebar --}}
        @include('components.colaborador-sidebar')
        
        {{-- Main Content --}}
        <main class="flex-1 p-4 overflow-y-auto">
            @yield('content')
        </main>
    </div>
 @include('partials.toast')
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>