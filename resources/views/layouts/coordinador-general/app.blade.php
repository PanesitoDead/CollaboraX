<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CollaboraX - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tab-transition {
            transition: all 0.2s ease-in-out;
        }
        .form-transition {
            transition: all 0.3s ease-in-out;
        }
        .hover-scale {
            transition: transform 0.2s ease-in-out;
        }
        .hover-scale:hover {
            transform: scale(1.02);
        }
        .notification-badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .slide-in {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-blue-900 text-white flex flex-col sidebar-transition">
            <!-- Logo -->
            <div class="p-6 border-b border-blue-800">
                <h1 class="text-xl font-bold">CollaboraX</h1>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('coordinador-general.dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('coordinador-general.dashboard') ? 'bg-blue-800' : 'hover:bg-blue-800' }} tab-transition hover-scale">
                            <i data-lucide="home" class="w-5 h-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('coordinador-general.metas') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('coordinador-general.metas') ? 'bg-blue-800' : 'hover:bg-blue-800' }} tab-transition hover-scale">
                            <i data-lucide="target" class="w-5 h-5"></i>
                            <span>Metas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('coordinador-general.actividades') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('coordinador-general.actividades') ? 'bg-blue-800' : 'hover:bg-blue-800' }} tab-transition hover-scale">
                            <i data-lucide="activity" class="w-5 h-5"></i>
                            <span>Actividades</span>
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('coordinador-general.equipos') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('coordinador-general.equipos') ? 'bg-blue-800' : 'hover:bg-blue-800' }} tab-transition hover-scale">
                            <i data-lucide="users" class="w-5 h-5"></i>
                            <span>Equipos</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('coordinador-general.reuniones') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('coordinador-general.reuniones') ? 'bg-blue-800' : 'hover:bg-blue-800' }} tab-transition hover-scale">
                            <i data-lucide="calendar" class="w-5 h-5"></i>
                            <span>Reuniones</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('coordinador-general.mensajes') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('coordinador-general.mensajes') ? 'bg-blue-800' : 'hover:bg-blue-800' }} tab-transition hover-scale relative">
                            <i data-lucide="message-circle" class="w-5 h-5"></i>
                            <span>Mensajes</span>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center notification-badge">3</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('coordinador-general.configuracion') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('coordinador-general.configuracion') ? 'bg-blue-800' : 'hover:bg-blue-800' }} tab-transition hover-scale">
                            <i data-lucide="settings" class="w-5 h-5"></i>
                            <span>Configuración</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- User Profile -->
            <div class="p-4 border-t border-blue-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-700 rounded-full flex items-center justify-center">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="font-medium">Coord. General</p>
                            <p class="text-sm text-blue-300">Supervisión</p>
                        </div>
                    </div>
                    <!-- Botón de cerrar sesión -->
                    <button 
                        onclick="cerrarSesion()" 
                        class="p-2 rounded-lg hover:bg-blue-800 transition-colors group" 
                        title="Cerrar sesión"
                    >
                        <i data-lucide="log-out" class="w-5 h-5 text-blue-300 group-hover:text-white"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            @yield('content')
        </div>
    </div>
    <!-- Formulario oculto para logout -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Add smooth transitions and animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-in animation to main content
            const mainContent = document.querySelector('.flex-1');
            if (mainContent) {
                mainContent.classList.add('fade-in');
            }
        });

        // Función para cerrar sesión
        function cerrarSesion() {
            // Confirmación antes de cerrar sesión
             document.getElementById('logout-form').submit();
        }
    </script>
</body>
</html>