{{-- Modal Estado de Pago --}}
<div id="modalEstadoPago" class="fixed inset-0 hidden z-50" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto modal-content" style="transform: scale(0.9); opacity: 0; transition: all 0.3s ease;">
            <div class="">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i data-lucide="info" class="w-5 h-5 mr-2 text-blue-600"></i>
                    Estado del Pago
                </h3>
                <button onclick="cerrarModalEstadoPago()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            {{-- Content --}}
            <div class="px-6 py-4" id="estadoPagoContent">
                {{-- El contenido se carga dinámicamente --}}
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 space-x-3">
                <button onclick="cerrarModalEstadoPago()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Cancelar Suscripción - INDEPENDIENTE, AL MISMO NIVEL --}}
<div id="modalCancelarSuscripcion" class="fixed inset-0 hidden z-50" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto modal-content" style="transform: scale(0.9); opacity: 0; transition: all 0.3s ease;">
            <div class="">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <span class="text-2xl mr-2">⚠️</span>
                    Cancelar Suscripción
                </h3>
                <button id="btnCerrarModalCancelar1" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            {{-- Content --}}
            <div class="px-6 py-4">
                <div class="text-center mb-6">
                    <div class="mb-6">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                            <i data-lucide="alert-triangle" class="h-8 w-8 text-red-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-3">¿Estás seguro?</h4>
                        <p class="text-gray-600 mb-4">
                            Esta acción cancelará tu suscripción y perderás el acceso a las funcionalidades premium.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-center px-6 py-4 border-t border-gray-200 space-x-4">
                <button id="btnCerrarModalCancelar2" 
                        class="px-6 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors">
                    No, mantener suscripción
                </button>
                <button id="btnConfirmarCancelacion" 
                        class="px-6 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
                    Sí, cancelar suscripción
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Los scripts de estos modales están en los archivos JavaScript modulares --}}
