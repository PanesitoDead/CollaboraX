{{-- resources/views/components/admin-sidebar.blade.php --}}
<div class="h-full bg-gradient-to-b from-blue-900 to-blue-800 text-white transition-all duration-300" style="width: 250px;">
    {{-- Header --}}
    <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
        <div class="flex items-center gap-2">
            <span class="font-semibold">CollaboraX</span>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="p-4 overflow-y-auto" style="height: calc(100% - 64px - 72px);">
        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 font-medium' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                </svg>
                <span>Dashboard</span>
            </a>

            {{-- <a href="{{ route('admin.areas') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('admin.areas.*') ? 'bg-white/20 font-medium' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span>Áreas</span>
            </a> --}}

            <a href="{{ route('admin.colaboradores') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('admin.colaboradores.*') ? 'bg-white/20 font-medium' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
                <span>Colaboradores</span>
            </a>

            {{-- <a href="{{ route('admin.coordinadores-equipo') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('admin.coordinadores-equipo.*') ? 'bg-white/20 font-medium' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Coord. Equipo</span>
            </a> --}}

            {{-- <a href="{{ route('admin.coordinadores-generales') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('admin.coordinadores-generales.*') ? 'bg-white/20 font-medium' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Coord. Generales</span>
            </a> --}}

            {{-- <a href="{{ route('admin.estadisticas') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('admin.estadisticas') ? 'bg-white/20 font-medium' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Estadísticas</span>
            </a> --}}

            {{-- <a href="{{ route('admin.configuracion') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('admin.configuracion') ? 'bg-white/20 font-medium' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Configuración</span>
            </a> --}}
        </nav>
    </div>

    {{-- User Info --}}
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10">
        <div class="flex items-center space-x-3">
            <div class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center border-2 border-white/20">
                <span class="text-sm font-medium">AD</span>
            </div>
            <div class="flex flex-col">
                <span class="text-sm font-medium">Administrador</span>
                <span class="text-xs text-white/70">Gestión</span>
            </div>
        </div>
    </div>
</div>