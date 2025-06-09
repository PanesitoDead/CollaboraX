<div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-300">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Invitación
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fechas
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invitaciones as $inv)
                    <tr class="hover:bg-gray-50">
                        {{-- Invitación: Área, Equipo y Coordinador --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{-- Área --}}
                            <div class="mb-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i data-lucide="layers" class="w-4 h-4 mr-1"></i>
                                    {{ $inv->area->nombre }}
                                </span>
                            </div>
                            {{-- Equipo --}}
                            <div class="flex items-center text-sm font-medium text-gray-900">
                                <i data-lucide="users" class="w-4 h-4 text-gray-400 mr-2"></i>
                                <span>{{ $inv->equipo->nombre ?? 'Sin equipo' }}</span>
                            </div>
                            {{-- Coordinador --}}
                            <div class="flex items-center mt-2 text-sm text-gray-500">
                                <div class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                    <span class="text-xs font-medium">{{ $inv->coordinador_iniciales }}</span>
                                </div>
                                <span>{{ $inv->coordinador_nombre_completo }}</span>
                            </div>
                        </td>

                        {{-- Fechas: Invitación, Expiración, Respuesta --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 space-y-1">
                            {{-- Fecha Invitación --}}
                            <div class="flex items-center">
                                <i data-lucide="calendar" class="w-4 h-4 text-gray-400 mr-1"></i>
                                <span>{{ $inv->fecha_invitacion }}</span>
                            </div>
                            {{-- Fecha Expiración --}}
                            @if($inv->fecha_expiracion)
                                <div class="flex items-center">
                                    <i data-lucide="clock" class="w-4 h-4 text-gray-400 mr-1"></i>
                                    <span>{{ $inv->fecha_expiracion }}</span>
                                </div>
                            @else
                                <div class="flex items-center text-gray-400">
                                    <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                                    <span>Sin expiración</span>
                                </div>
                            @endif
                            {{-- Fecha Respuesta --}}
                            @if($inv->fecha_respuesta)
                                <div class="flex items-center">
                                    <i data-lucide="check" class="w-4 h-4 text-gray-400 mr-1"></i>
                                    <span>{{ $inv->fecha_respuesta }}</span>
                                </div>
                            @else
                                <div class="flex items-center text-gray-400">
                                    <i data-lucide="help-circle" class="w-4 h-4 mr-1"></i>
                                    <span>Sin respuesta</span>
                                </div>
                            @endif
                        </td>

                        {{-- Estado --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                switch ($inv->estado) {
                                    case 'ACEPTADA':
                                        $badgeBg = 'bg-green-100 text-green-800';
                                        $dotBg = 'bg-green-400';
                                        break;
                                    case 'RECHAZADA':
                                        $badgeBg = 'bg-red-100 text-red-800';
                                        $dotBg = 'bg-red-400';
                                        break;
                                    default:
                                        $badgeBg = 'bg-yellow-100 text-yellow-800';
                                        $dotBg = 'bg-yellow-400';
                                        break;
                                }
                            @endphp
                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeBg }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $dotBg }}"></span>
                                {{ ucfirst($inv->estado) }}
                            </div>
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($inv->estado === 'PENDIENTE')
                                <button
                                    onclick="aceptarInvitacion({{ $inv->id }})"
                                    class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors mr-2">
                                    <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                                    Aceptar
                                </button>
                                <button
                                    onclick="rechazarInvitacion({{ $inv->id }})"
                                    class="inline-flex items-center px-3 py-2 border border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                                    <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i>
                                    Rechazar
                                </button>
                            @else
                                <button
                                    onclick="verDetallesInvitacion({{ $inv->id }})"
                                    class="inline-flex items-center px-3 py-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-full transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                    Ver
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i data-lucide="inbox" class="mx-auto h-12 w-12 text-gray-400"></i>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay invitaciones registradas.</h3>
                            <p class="mt-1 text-sm text-gray-500">Cuando haya invitaciones, aparecerán aquí.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($invitaciones->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $invitaciones->links() }}
        </div>
    @endif
</div>

<!-- Modal para ver detalles de la invitación -->
@include('partials.colaborador.modales.detalles.invitacion-modal-detalles')
<!-- Modal para confirmar aceptación/rechazo de invitación -->
@include('partials.colaborador.modales.confirmacion.invitacion-modal-confirmacion')

<script>
    // Atajos para los botones de la tabla
    function aceptarInvitacion(id) {
        abrirModalInvitacion(id, 'aceptar');
    }

    function rechazarInvitacion(id) {
        abrirModalInvitacion(id, 'rechazar');
    }

    function verDetallesInvitacion(id) {
        abrirModalDetallesInvitacion(id);
    }
</script>
