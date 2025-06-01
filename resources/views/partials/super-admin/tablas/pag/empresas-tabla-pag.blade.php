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
                                @if (!empty($e->avatar))
                                    <img id="avatarEmpresa" src="{{ $e->avatar }}" alt="Avatar de la empresa" class="w-full h-full object-cover" />
                                @else
                                    <span class="text-sm font-medium">
                                        {{ strtoupper(substr($e->nombre, 0, 2)) }}
                                    </span>
                                @endif
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="inline-flex items-center px-3 py-2">
                                <i data-lucide="users" class="w-4 h-4 mr-1 text-gray-500"></i>
                                {{ $e->nro_usuarios() }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $e->activo ? 'bg-green-100 text-green-800':'bg-red-100 text-red-800' }}">
                                {{ ucfirst($e->activo ? 'Activo' : 'Inactivo') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $e->fecha_registro}}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="abrirModalDetallesEmpresa({{ $e->id }})" class="inline-flex items-center px-3 py-2 text-blue-600 hover:text-blue-900 transition-colors">
                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                Ver
                            </button>
                            <button onclick="abrirModalEmpresa({{$e->id}})" class="inline-flex items-center px-3 py-2 text-blue-600 hover:text-blue-900 transition-colors">
                                <i data-lucide="edit" class="w-4 h-4 mr-1"></i>
                                Editar
                            </button>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input
                                    type="checkbox"
                                    class="sr-only peer"
                                    {{ $e->activo ? 'checked' : '' }}
                                    onclick="confirmarCambioEstado(event, {{ $e->id }}, {{ $e->activo ? 'true' : 'false' }})"
                                />
                                <div
                                    class="w-10 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-offset-1 peer-focus:ring-blue-400
                                            rounded-full peer peer-checked:bg-blue-500 
                                            after:content-[''] after:absolute after:top-[2px] after:left-[3px]
                                            after:bg-white after:border after:border-gray-300 after:rounded-full 
                                            after:h-4 after:w-4 after:shadow-md after:transition-transform
                                            peer-checked:after:translate-x-[18px]"
                                ></div>
                            </label>
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

<!-- Modal para ver detalles de la empresa -->
@include('partials.super-admin.modales.detalles.empresas-modal-detalles')
<!-- Modal para crear/editar empresa -->
@include('partials.super-admin.modales.edicion.empresas-modal-edicion')
<!-- Modal para cambiar estado de la empresa -->
@include('partials.super-admin.modales.confirmacion.empresas-modal-confirmacion')

<script>
    function confirmarCambioEstado(event, id, estadoActual) {
    event.preventDefault(); // evita que el checkbox cambie su estado
    abrirModalConfirmacion(id, estadoActual);
}
</script>