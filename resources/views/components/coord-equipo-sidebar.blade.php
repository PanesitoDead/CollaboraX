<div class="fixed left-0 top-0 z-30 h-full bg-gradient-to-b from-blue-800 to-blue-900 text-white shadow-xl transition-all duration-300" style="width: var(--sidebar-width);">
    <div class="flex h-full flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-blue-700">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-white/20 flex items-center justify-center">
                    <i data-lucide="users" class="h-5 w-5"></i>
                </div>
                <span class="font-semibold text-lg sidebar-text">CollaboraX</span>
            </div>
            <button onclick="toggleSidebar()" class="p-1 rounded hover:bg-white/10 transition-colors">
                <i data-lucide="menu" class="h-5 w-5"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('coord-equipo.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('coord-equipo.dashboard') ? 'bg-white/20' : '' }}">
                <i data-lucide="layout-dashboard" class="h-5 w-5"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors">
                <i data-lucide="target" class="h-5 w-5"></i>
                <span class="sidebar-text">Metas</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors">
                <i data-lucide="check-square" class="h-5 w-5"></i>
                <span class="sidebar-text">Actividades</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors">
                <i data-lucide="users" class="h-5 w-5"></i>
                <span class="sidebar-text">Equipos</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors">
                <i data-lucide="calendar" class="h-5 w-5"></i>
                <span class="sidebar-text">Reuniones</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors">
                <i data-lucide="message-circle" class="h-5 w-5"></i>
                <span class="sidebar-text">Mensajes</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors">
                <i data-lucide="settings" class="h-5 w-5"></i>
                <span class="sidebar-text">Configuración</span>
            </a>
        </nav>

        <!-- User info -->
        <div class="p-4 border-t border-blue-700">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center">
                    <span class="text-sm font-medium">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                </div>
                <div class="sidebar-text">
                    <div class="text-sm font-medium">{{ auth()->user()->name ?? 'Usuario' }}</div>
                    <div class="text-xs text-blue-200">Coordinador de Grupo</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .sidebar-collapsed .sidebar-text {
        display: none;
    }
    
    .sidebar-collapsed nav a {
        justify-content: center;
    }
    
    .sidebar-collapsed .flex.items-center.gap-3 {
        justify-content: center;
    }
</style>