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
            {{-- <a href="{{ route('colaborador.dashboard') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('colaborador.dashboard') ? 'bg-white/20 font-medium' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                </svg>
                <span>Dashboard</span>
            </a> --}}

            <a href="{{ route('colaborador.actividades') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('colaborador.actividades.*') ? 'bg-white/20 font-medium' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V7l-4-4zM16 3v4h4M8 11h8m-8 4h8m-8-8h8"/>
                </svg>
                <span>Actividades</span>
            </a>

            <a href="{{ route('colaborador.mensajes') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('colaborador.mensajes.*') ? 'bg-white/20 font-medium' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m5 8l-5-5H6a2 2 0 01-2-2V6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2z" />
                </svg>

                <span>Mensajes</span>
            </a>

            <a href="{{ route('colaborador.reuniones') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('colaborador.reuniones.*') ? 'bg-white/20 font-medium' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 7h8a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2z" />
                </svg>

                <span>Reuniones</span>
            </a>

            <a href="{{ route('colaborador.invitaciones') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('colaborador.invitaciones.*') ? 'bg-white/20 font-medium' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 0l2.5-2.5M12 12l-2.5-2.5M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Invitaciones</span>
            </a>

            <a href="{{ route('colaborador.mi-equipo') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('colaborador.mi-equipo.*') ? 'bg-white/20 font-medium' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14a4 4 0 100-8 4 4 0 000 8zm0 2a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM6.5 20H5a2 2 0 01-2-2v-1a2 2 0 012-2h1.5m13.5 0H19a2 2 0 012 2v1a2 2 0 01-2 2h-1.5"/>
                </svg>
                <span>Mi Equipo</span>
            </a>

            <a href="{{ route('colaborador.configuracion') }}" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-all duration-200 hover:bg-white/10 {{ request()->routeIs('colaborador.configuracion') ? 'bg-white/20 font-medium' : '' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Configuración</span>
            </a>
        </nav>
    </div>

    {{-- User Info --}}
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10">
        <div class="flex items-center space-x-3">
            <div class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center border-2 border-white/20">
                <span class="text-sm font-medium">AD</span>
            </div>
            <div class="flex flex-col">
                <span class="text-sm font-medium">Colaborador</span>
                <span class="text-xs text-white/70">Gestión</span>
            </div>
        </div>
    </div>
</div>