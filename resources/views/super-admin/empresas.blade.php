{{-- resources/views/super-admin/empresas.blade.php --}}
@extends('layouts.super-admin.super-admin')

@section('title', 'Gestión de Empresas')
@section('page-title', 'Gestión de Empresas')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Empresas Registradas</h1>
            <p class="text-gray-600">Gestiona todas las empresas del sistema</p>
        </div>
        <div class="flex items-center gap-2">
            <button id="exportBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                Exportar Datos
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300">
        <form class="flex flex-wrap gap-4" method="GET">
            <div class="relative flex-1 min-w-64">
                <i data-lucide="search" class="absolute left-3 top-3 h-4 w-4 text-gray-400"></i>
                <input type="text" name="searchTerm" value="{{ request('searchTerm') }}" placeholder="Buscar por nombre o email..."
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div class="min-w-48 relative">
                <select name="filters[plan_servicio_id]" onchange="this.form.submit()"
                    class="w-full pr-4 pl-3 py-2 border border-gray-300 rounded-lg
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        appearance-none">
                    <option value="">Todos los planes</option>
                    <option value="1" {{ request('filters.plan_servicio_id')=='1'?'selected':'' }}>Standard</option>
                    <option value="2" {{ request('filters.plan_servicio_id')=='2'?'selected':'' }}>Business</option>
                    <option value="3" {{ request('filters.plan_servicio_id')=='3'?'selected':'' }}>Enterprise</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                    <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500"></i>
                </div>
            </div>
            <div class="min-w-48 relative">
                <select name="filters[estado]" onchange="this.form.submit()"
                    class="w-full pr-4 pl-3 py-2 border border-gray-300 rounded-lg
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        appearance-none">
                    <option value="">Todos los estados</option>
                    <option value="1" {{ request('filters.estado')=='1'?'selected':'' }}>Activo</option>
                    <option value="0" {{ request('filters.estado')=='0'?'selected':'' }}>Inactivo</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                    <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500"></i>
                </div>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                Filtrar
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-300">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuarios</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($empresas as $e)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">{{ substr($e->nombre,0,2) }}</span>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $e->nombre }}</p>
                                    <p class="text-sm text-gray-500">{{ $e->correo }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{
                                    $e->plan_servicio=='Enterprise'? 'bg-purple-100 text-purple-800': (
                                    $e->plan_servicio=='Business'? 'bg-blue-100 text-blue-800': 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($e->plan_servicio) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $e->nro_usuarios }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $e->activo ? 'bg-green-100 text-green-800':'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($e->activo ? 'Activo' : 'Inactivo') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $e->fecha_registro}}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="openDetailsCompanyModal($e->id)" class="text-blue-600 hover:text-blue-900 mr-2">Ver</button>
                                <button onclick="openCompanyModal($e->id)" class="text-green-600 hover:text-green-900 mr-2">Editar</button>
                                <button onclick="suspenderEmpresa({{ $e->id }})" class="text-red-600 hover:text-red-900">Suspender</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No hay empresas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($empresas->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $empresas->links() }}
            </div>
        @endif
    </div>
</div>
<div id="companyModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="companyModalTitle">
  <div class="fixed inset-0 bg-black/50" onclick="closeCompanyModal()"></div>
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg flex flex-col overflow-hidden min-h-0">
      <form id="companyForm" method="POST" enctype="multipart/form-data" class="flex flex-col h-full">
        @csrf
        <div id="companyMethodField"></div>

        <!-- Header fijo -->
        <header class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0">
          <h3 id="companyModalTitle" class="text-lg font-semibold text-gray-900">Editar Empresa</h3>
          <button type="button" onclick="closeCompanyModal()" aria-label="Cerrar modal" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
          </button>
        </header>

        <!-- Contenido scrollable -->
        <div class="px-6 py-4 space-y-4 overflow-y-auto flex-1 max-h-[70vh] min-h-0">
          <div>
            <label for="plan" class="block mb-1 text-sm font-medium text-gray-700">Plan</label>
            <select name="plan" id="plan" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="standard">Standard</option>
              <option value="business">Business</option>
              <option value="enterprise">Enterprise</option>
            </select>
          </div>

          <div>
            <label for="company_name" class="block mb-1 text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" name="name" id="company_name" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
          </div>

          <div>
            <label for="company_description" class="block mb-1 text-sm font-medium text-gray-700">Descripción</label>
            <textarea name="description" id="company_description" rows="3" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label for="company_ruc" class="block mb-1 text-sm font-medium text-gray-700">RUC</label>
              <input type="text" name="ruc" id="company_ruc" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label for="company_phone" class="block mb-1 text-sm font-medium text-gray-700">Teléfono</label>
              <input type="tel" name="phone" id="company_phone" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
          </div>

          <!-- Foto Perfil con vista previa -->
          <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Foto Perfil</label>
            <div class="flex items-center space-x-4">
              <div class="w-20 h-20 rounded-full overflow-hidden border">
                <img id="companyAvatarPreview" src="" alt="Avatar Actual" class="w-full h-full object-cover" />
              </div>
              <div>
                <input type="file" name="avatar" id="company_avatar" accept="image/*" class="mt-1" />
                <p class="mt-1 text-xs text-gray-500">Elige una nueva foto para actualizar.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer fijo -->
        <footer class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-2 flex-shrink-0">
          <button type="button" onclick="closeCompanyModal()" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <span id="companySubmitText">Actualizar Empresa</span>
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>

<!-- Modal para ver detalles de la empresa -->
<div id="detailsCompanyModal" class="fixed inset-0 bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300" role="dialog" aria-modal="true" aria-labelledby="detailsCompanyModalTitle">
  <div class="flex items-center justify-center min-h-screen p-4">
    <div id="detailsCompanyContent" class="bg-white rounded-2xl shadow-xl max-w-lg w-full transform scale-95 transition-transform duration-300">
      <!-- Header -->
      <div class="px-6 py-4 flex justify-between items-center border-b border-gray-200 bg-gray-50 rounded-t-2xl">
        <h3 id="detailsCompanyModalTitle" class="text-lg font-semibold text-gray-900">Detalles de Empresa</h3>
        <button type="button" onclick="closeDetailsCompanyModal()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Cerrar detalles">
          <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
        </button>
      </div>
      <!-- Contenido -->
      <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
        <div class="flex items-center space-x-4 mb-4">
          <div class="w-20 h-20 rounded-full overflow-hidden border">
            <img id="detailsAvatar" src="" alt="Avatar Empresa" class="w-full h-full object-cover" />
          </div>
          <div>
            <h4 id="detailsName" class="text-lg font-medium text-gray-900"></h4>
            <p id="detailsEmail" class="text-sm text-gray-500"></p>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <p class="font-semibold text-gray-700 mb-1">Plan</p>
            <p id="detailsPlan" class="text-gray-900"></p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">RUC</p>
            <p id="detailsRuc" class="text-gray-900"></p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">Teléfono</p>
            <p id="detailsPhone" class="text-gray-900"></p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">Estado</p>
            <span id="detailsStatus" class="inline-block px-2 py-1 text-xs font-semibold rounded-full"></span>
          </div>
          <div class="col-span-2">
            <p class="font-semibold text-gray-700 mb-1">Descripción</p>
            <p id="detailsDescription" class="text-gray-900"></p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">Fecha de Registro</p>
            <p id="detailsRegistered" class="text-gray-900"></p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">Usuarios</p>
            <p class="text-gray-900 flex items-center text-xs"><i data-lucide="users" class="w-4 h-4 mr-1 text-gray-500"></i><span id="detailsUsersCount"></span></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
    let currentCompanyId = null;

    function openCompanyModal() {
        document.getElementById('companyModalTitle').textContent = currentCompanyId ? 'Editar Empresa' : 'Nueva Empresa';
        document.getElementById('companySubmitText').textContent = currentCompanyId ? 'Actualizar Empresa' : 'Crear Empresa';
        document.getElementById('companyMethodField').innerHTML = currentCompanyId ? '@method("PUT")' : '';
        document.getElementById('companyForm').reset();
        if (currentCompanyId) document.getElementById('companyForm').action = `/admin/companies/${currentCompanyId}`;
        document.getElementById('companyModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCompanyModal() {
        document.getElementById('companyModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentCompanyId = null;
    }

    function editCompany(id) {
        currentCompanyId = id;
        fetch(`/admin/companies/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                for (let key of ['plan','name','description','ruc','phone']) {
                    const el = document.getElementById(`company_${key}`);
                    if (el) el.value = data[key] ?? '';
                }
                openCompanyModal();
            });
    }

    let detailsCompanyId = null;
  function openDetailsCompanyModal(data) {
    detailsCompanyId = data.id;
    document.getElementById('detailsAvatar').src = data.avatar_url || '/images/default-avatar.png';
    document.getElementById('detailsName').textContent = data.name;
    document.getElementById('detailsEmail').textContent = data.email;
    document.getElementById('detailsPlan').textContent = data.plan;
    document.getElementById('detailsRuc').textContent = data.ruc;
    document.getElementById('detailsPhone').textContent = data.phone;
    // Estado con colores
    const statusEl = document.getElementById('detailsStatus');
    const isActive = data.status === 'active';
    statusEl.textContent = isActive ? 'Activo' : 'Inactivo';
    statusEl.className = `inline-block px-2 py-1 text-xs font-semibold rounded-full ${isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
    document.getElementById('detailsDescription').textContent = data.description;
    document.getElementById('detailsRegistered').textContent = new Date(data.registered_at).toLocaleDateString();
    document.getElementById('detailsUsersCount').textContent = data.users_count;
    const modal = document.getElementById('detailsCompanyModal');
    const content = document.getElementById('detailsCompanyContent');
    modal.classList.remove('hidden');
    setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    document.body.style.overflow = 'hidden';
  }
  function closeDetailsCompanyModal() {
    document.getElementById('detailsCompanyModal').classList.add('opacity-0');
    document.getElementById('detailsCompanyContent').classList.add('scale-95');
    setTimeout(() => {
      document.getElementById('detailsCompanyModal').classList.add('hidden');
      document.body.style.overflow = 'auto';
      detailsCompanyId = null;
    }, 300);
  }

    document.querySelectorAll('#companyModal').forEach(modal => {
        modal.addEventListener('click', e => {
            if (e.target === modal) closeCompanyModal();
        });
    });
</script>
@endpush
