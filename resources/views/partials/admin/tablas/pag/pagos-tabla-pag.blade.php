<div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-300">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pago</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($historialPagos ?? [] as $pago)
                    <tr class="hover:bg-gray-50">
                        <!-- ID Pago -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">#{{ $pago['id'] ?? 'N/A' }}</div>
                        </td>

                        <!-- Plan -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $pago['plan']['nombre'] ?? $pago['plan_nombre'] ?? 'Plan no especificado' }}
                            </div>
                        </td>

                        <!-- Monto -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                S/ {{ number_format($pago['monto'] ?? 0, 2) }}
                            </div>
                        </td>

                        <!-- Estado -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if(isset($pago['estado']) && $pago['estado'] === 'approved') bg-green-100 text-green-800
                                @elseif(isset($pago['estado']) && $pago['estado'] === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif
                            ">
                                @if(isset($pago['estado']) && $pago['estado'] === 'approved')
                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                    Aprobado
                                @elseif(isset($pago['estado']) && $pago['estado'] === 'pending')
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                    Pendiente
                                @else
                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                    {{ isset($pago['estado']) ? ucfirst($pago['estado']) : 'Rechazado' }}
                                @endif
                            </span>
                        </td>

                        <!-- Fecha -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if(isset($pago['fecha_pago']))
                                    {{ date('d/m/Y', strtotime($pago['fecha_pago'])) }}
                                @elseif(isset($pago['created_at']))
                                    {{ date('d/m/Y', strtotime($pago['created_at'])) }}
                                @else
                                    No disponible
                                @endif
                            </div>
                        </td>

                        <!-- Acciones -->
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button onclick="mostrarDetallesPago({{ json_encode($pago) }})" 
                                    class="text-blue-600 hover:text-blue-900 text-sm font-medium transition-colors duration-200">
                                <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                Ver detalles
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="credit-card" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay pagos registrados</h3>
                                <p class="text-gray-500 mb-4">Aún no se han procesado pagos para esta cuenta.</p>
                                <button onclick="abrirModalSeleccionarPlan()" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                    Realizar Primer Pago
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if(isset($historialPagos) && count($historialPagos) > 0)
        <div class="bg-white px-4 py-3 border-t border-gray-300 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <p class="text-sm text-gray-700">
                        Mostrando <span class="font-medium">{{ $paginacion['desde'] ?? 1 }}</span> a 
                        <span class="font-medium">{{ $paginacion['hasta'] ?? count($historialPagos) }}</span> de 
                        <span class="font-medium">{{ $paginacion['total'] ?? count($historialPagos) }}</span> pagos
                    </p>
                </div>
                @if(isset($paginacion) && $paginacion['total_paginas'] > 1)
                    <div class="flex items-center space-x-2">
                        @if($paginacion['pagina_actual'] > 1)
                            <button onclick="cargarPagina({{ $paginacion['pagina_actual'] - 1 }})" 
                                    class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 transition-colors">
                                Anterior
                            </button>
                        @endif
                        
                        <span class="px-3 py-2 text-sm text-gray-700">
                            Página {{ $paginacion['pagina_actual'] }} de {{ $paginacion['total_paginas'] }}
                        </span>
                        
                        @if($paginacion['pagina_actual'] < $paginacion['total_paginas'])
                            <button onclick="cargarPagina({{ $paginacion['pagina_actual'] + 1 }})" 
                                    class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 transition-colors">
                                Siguiente
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Modal de Detalles del Pago -->
<div id="modalDetallesPago" class="fixed inset-0 hidden z-50" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto modal-content" style="transform: scale(0.9); opacity: 0; transition: all 0.3s ease;">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Detalles del Pago</h3>
                    <button onclick="cerrarModalDetalles()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
            <div class="px-6 py-4">
                <div id="contenidoDetallesPago" class="space-y-4">
                    <!-- El contenido se llenará dinámicamente -->
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button onclick="cerrarModalDetalles()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function mostrarDetallesPago(pago) {
    const modal = document.getElementById('modalDetallesPago');
    const contenido = document.getElementById('contenidoDetallesPago');
    
    // Formatear fecha
    let fechaFormateada = 'No disponible';
    if (pago.fecha_pago) {
        fechaFormateada = new Date(pago.fecha_pago).toLocaleString('es-PE');
    } else if (pago.created_at) {
        fechaFormateada = new Date(pago.created_at).toLocaleString('es-PE');
    }
    
    // Generar contenido del modal
    contenido.innerHTML = `
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">ID de Pago</label>
                <p class="mt-1 text-sm text-gray-900">#${pago.id || 'N/A'}</p>
            </div>
            
            ${pago.referencia_ext ? `
            <div>
                <label class="block text-sm font-medium text-gray-700">Referencia Externa</label>
                <p class="mt-1 text-sm text-gray-900">${pago.referencia_ext}</p>
            </div>
            ` : ''}
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Plan</label>
                <p class="mt-1 text-sm text-gray-900">${pago.plan?.nombre || pago.plan_nombre || 'Plan no especificado'}</p>
                ${pago.plan?.frecuencia || pago.frecuencia ? `
                <p class="text-xs text-gray-500">${(pago.plan?.frecuencia || pago.frecuencia || 'mensual').charAt(0).toUpperCase() + (pago.plan?.frecuencia || pago.frecuencia || 'mensual').slice(1)}</p>
                ` : ''}
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Monto</label>
                <p class="mt-1 text-sm text-gray-900">${pago.moneda || 'PEN'} ${parseFloat(pago.monto || 0).toFixed(2)}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <p class="mt-1 text-sm text-gray-900">${pago.estado ? pago.estado.charAt(0).toUpperCase() + pago.estado.slice(1) : 'No especificado'}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Método de Pago</label>
                <p class="mt-1 text-sm text-gray-900">${pago.metodo_pago || 'MercadoPago'}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha y Hora</label>
                <p class="mt-1 text-sm text-gray-900">${fechaFormateada}</p>
            </div>
            
            ${pago.descripcion ? `
            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <p class="mt-1 text-sm text-gray-900">${pago.descripcion}</p>
            </div>
            ` : ''}
        </div>
    `;
    
    modal.classList.remove('hidden');
    // Trigger animation
    setTimeout(() => {
        modal.querySelector('.modal-content').style.transform = 'scale(1)';
        modal.querySelector('.modal-content').style.opacity = '1';
    }, 10);
}

function cerrarModalDetalles() {
    const modal = document.getElementById('modalDetallesPago');
    const modalContent = modal.querySelector('.modal-content');
    
    modalContent.style.transform = 'scale(0.9)';
    modalContent.style.opacity = '0';
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Cerrar modal al hacer clic fuera de él
document.getElementById('modalDetallesPago').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalDetalles();
    }
});
</script>
