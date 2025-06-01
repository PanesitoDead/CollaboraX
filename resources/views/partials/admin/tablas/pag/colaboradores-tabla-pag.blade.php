<div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-300">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($coordinadores as $c)
                    <tr class="hover:bg-gray-50">
                        <!-- Nombre -->
                        <td class="px-6 py-4 whitespace-nowrap flex items-center">
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                @php
                                    $initials = strtoupper(substr($c->apellido_paterno, 0, 1) . substr($c->apellido_materno, 0, 1));
                                @endphp
                                <span class="text-sm font-medium">{{ $initials }}</span>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $c->nombres }} {{ $c->apellido_paterno }} {{ $c->apellido_materno }}
                                </p>
                            </div>
                        </td>

                        <!-- Correo -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $c->correo }}
                        </td>

                        <!-- Área -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $c->area ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $c->area->nombre ?? 'Sin área' }}
                            </span>
                        </td>

                        <!-- Equipo -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($c->equipo)
                                {{ $c->equipo->nombre }}
                            @else
                                <span class="text-gray-400">Sin equipo</span>
                            @endif
                        </td>

                        <!-- Estado -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors {{ $c->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $c->activo ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                {{ ucfirst($c->activo ? 'Activo' : 'Inactivo') }}
                            </div>
                        </td>

                        <!-- Fecha Registro -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $c->fecha_registro }}
                        </td>

                        <!-- Acciones -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button
                                onclick="abrirModalDetallesColaborador({{ $c->id }})"
                                class="inline-flex items-center px-3 py-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-full transition-colors">
                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                Ver
                            </button>
                            <button
                                onclick="abrirModalEditarColaborador({{ $c->id }})"
                                class="inline-flex items-center px-3 py-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-full transition-colors">
                                <i data-lucide="edit" class="w-4 h-4 mr-1"></i>
                                Editar
                            </button>
                            <label class="top-[3px] relative inline-flex items-center cursor-pointer">
                                <input
                                    type="checkbox"
                                    class="sr-only peer"
                                    {{ $c->activo ? 'checked' : '' }}
                                    onclick="confirmarCambioEstadoColaborador(event, {{ $c->id }}, {{ $c->activo ? 'true' : 'false' }})"
                                />
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-offset-1 peer-focus:ring-blue-400
                                           rounded-full peer peer-checked:bg-blue-500
                                           after:content-[''] after:absolute after:top-[2px] after:left-[3px]
                                           after:bg-white after:border after:border-gray-300 after:rounded-full
                                           after:h-5 after:w-5 after:shadow-md after:transition-transform
                                           peer-checked:after:translate-x-[18px]">
                                </div>
                            </label>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i data-lucide="users" class="mx-auto h-12 w-12 text-gray-400"></i>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay coordinadores registrados.</h3>
                            <p class="mt-1 text-sm text-gray-500">Agrega nuevos coordinadores para comenzar.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($coordinadores->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $coordinadores->links() }}
        </div>
    @endif
</div>

<!-- Modal para ver detalles deel colaborador -->
@include('partials.admin.modales.detalles.colaborador-modal-detalles')
<!-- Modal para crear/editar empresa -->
{{-- @include('partials.super-admin.modales.edicion.empresas-modal-edicion') --}}
<!-- Modal para cambiar estado de la empresa -->
{{-- @include('partials.super-admin.modales.confirmacion.empresas-modal-confirmacion') --}}

<script>
    function confirmarCambioEstado(event, id, estadoActual) {
    event.preventDefault(); // evita que el checkbox cambie su estado
    abrirModalConfirmacion(id, estadoActual);
}
</script>