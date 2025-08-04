{{-- resources/views/components/admin-sidebar.blade.php --}}
<style>
  /* Diseño base compacto */
  #sidebar {
    width: 240px;
    transition: all 0.3s ease;
    background: linear-gradient(to bottom, #1e3a8a, #1e40af);
    overflow: visible;
  }

  /* Estado colapsado más compacto */
  #sidebar.collapsed {
    width: 72px;
  }

  /* Header ajustado */
  .sidebar-header {
    height: 100px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
  }

  /* Logo y flecha en estado expandido */
  #sidebar:not(.collapsed) .sidebar-header {
    height: 60px;
    padding: 0 1.5rem;
    justify-content: space-between;
    gap: 0.5rem;
  }

  /* Posición correcta de la flecha */
  .toggle-btn {
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    padding: 4px;
    transform: translateX(0);
  }

  /* Estado colapsado - CX arriba de la flecha */
  #sidebar.collapsed .sidebar-header {
    flex-direction: column;
    justify-content: center;
    gap: 6px;
    padding: 0;
  }

  /* Transición del logo */
  .sidebar-fullname {
    transition: opacity 0.2s;
    white-space: nowrap;
  }
  .sidebar-abbrev {
    opacity: 0;
    position: absolute;
    transition: all 0.3s;
    font-size: 14px;
  }
  #sidebar.collapsed .sidebar-fullname {
    opacity: 0;
    display: none;
  }
  #sidebar.collapsed .sidebar-abbrev {
    opacity: 1;
    position: static;
  }

  /* Items del menú compactos */
  .nav-links a {
    display: flex;
    align-items: center;
    padding: 10px 1rem;
    gap: 0.75rem;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    border-radius: 6px;
    margin: 2px 0px;
    transition: all 0.2s;
  }

  /* Estado activo */
  .nav-links a.bg-white\\/20 {
    border-radius: 6px;
  }

  /* Estado colapsado */
  #sidebar.collapsed nav a {
    justify-content: center;
    padding: 10px;
    margin: 2px 0;
  }
  #sidebar.collapsed .sidebar-label {
    display: none;
  }

  /* Ajuste final de posición */
  #sidebar:not(.collapsed) .toggle-btn {
    position: relative;
    right: -4px;
  }

  /* Nuevos estilos para el menú de usuario */
  #user-menu-button {
    transition: all 0.2s;
    padding: 8px;
    border-radius: 6px;
  }
  
  #user-menu-button:hover {
    background: rgba(255,255,255,0.1);
  }

  #user-menu {
    min-width: 200px;
    transform-origin: bottom;
    transition: all 0.2s;
    box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
    z-index: 50;
    background: rgba(245,245,245, 1);
    color: black;       /* Texto negro */
    border: 1px solid rgba(0,0,0,0.1);
  }

  .user-menu-item {
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    transition: all 0.2s;
    cursor: pointer;
  }

  .user-menu-item:hover {
    background: rgba(0,0,0,0.05);
  }
</style>
<div id="sidebar" class="h-full bg-gradient-to-b from-blue-900 to-blue-800 text-white relative">
  {{-- Header --}}
  <div class="sidebar-header">
    <div class="flex items-center gap-2">
      <span class="sidebar-fullname font-semibold">CollaboraX</span>
      <span class="sidebar-abbrev font-semibold">CX</span>
    </div>
    <button id="sidebar-toggle" class="toggle-btn">
      <i data-lucide="chevron-left" class="h-5 w-5"></i>
      <i data-lucide="chevron-right" class="h-5 w-5 hidden"></i>
    </button>
  </div>

  {{-- Navigation --}}
  <div class="p-4 overflow-y-auto" style="height: calc(100% - 64px - 80px);">
    <nav class="space-y-2 nav-links">
        @php
        $items = [
          ['route' => 'coord-equipo.dashboard', 'icon' => 'home', 'label' => 'Dashboard'],
          ['route' => 'coord-equipo.metas', 'icon' => 'flag', 'label' => 'Metas'],
          ['route' => 'coord-equipo.actividades', 'icon' => 'check-circle', 'label' => 'Actividades'],
          ['route' => 'coord-equipo.equipo', 'icon' => 'clipboard-list', 'label' => 'Mi Equipo'],
          ['route' => 'coord-equipo.reuniones', 'icon' => 'message-square', 'label' => 'Reuniones'],
          ['route' => 'coord-equipo.configuracion', 'icon' => 'calendar-clock', 'label' => 'Configuración'], 
          ['route' => 'coord-equipo.mensajes', 'icon' => 'mail', 'label' => 'Mensajes'], 
            // ['route' => 'coord-equipo.mi-equipo', 'icon' => 'users', 'label' => 'Mi Equipo'],
            // ['route' => 'coord-equipo.configuracion', 'icon' => 'settings', 'label' => 'Configuración'],
        ];
      @endphp
      @foreach ($items as $item)
        <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['route'] . '*') ? 'bg-white/20' : '' }}">
          <i data-lucide="{{ $item['icon'] }}" class="h-5 w-5"></i>
          <span class="sidebar-label">{{ $item['label'] }}</span>
        </a>
      @endforeach
    </nav>
  </div>

  {{-- User Info with logout dropdown --}}
    <div class="absolute bottom-0 left-0 w-full px-3 pb-4">
    <div class="relative">
        <button id="user-menu-button" type="button" 
                class="flex items-center gap-3 w-full text-left hover:bg-white/10 rounded-lg"
                onclick="toggleUserMenu()">
        <div class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center border-2 border-white/20 shrink-0">
            <span class="text-sm font-medium">CO</span>
        </div>
        <div class="flex flex-col flex-1 sidebar-label">
            <span class="text-sm font-medium">Colaborador</span>
            <span class="text-xs text-white/70">Gestión</span>
        </div>
        <i data-lucide="chevron-up" class="h-4 w-4 sidebar-label" id="user-menu-arrow"></i>
        </button>
        
        <div id="user-menu" class="hidden absolute bottom-full left-0 mb-2 w-full bg-gradient-to-b from-blue-900 to-blue-800 text-white rounded-lg border border-white/10">
            <a href="{{ route('coord-equipo.configuracion') }}" class="user-menu-item">
                <i data-lucide="user" class="h-5 w-5"></i>
                <span>Perfil</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="user-menu-item w-full">
                    <i data-lucide="log-out" class="h-5 w-5"></i>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </div>
    </div>
</div>

@push('scripts')
<script>
  function toggleUserMenu() {
    const menu = document.getElementById('user-menu');
    const arrow = document.getElementById('user-menu-arrow');
    menu.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
  }

  // Cerrar menú al hacer click fuera
  document.addEventListener('click', function(event) {
    const userMenu = document.getElementById('user-menu');
    const userButton = document.getElementById('user-menu-button');
    
    if (!userButton.contains(event.target) && !userMenu.contains(event.target)) {
      userMenu.classList.add('hidden');
      document.getElementById('user-menu-arrow').classList.remove('rotate-180');
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebar-toggle');
    
    // Inicializar Lucide primero
    lucide.createIcons();
    
    toggle.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      // Actualizar íconos correctamente
      lucide.createIcons();
    });
  });
</script>
@endpush