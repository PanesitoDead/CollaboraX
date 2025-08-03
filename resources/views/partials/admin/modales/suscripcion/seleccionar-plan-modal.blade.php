{{-- Modal Seleccionar Plan --}}
<div id="modalSeleccionarPlan" class="fixed inset-0 hidden z-50" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto modal-content" style="transform: scale(0.9); opacity: 0; transition: all 0.3s ease;">
            <div class="">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i data-lucide="credit-card" class="w-5 h-5 mr-2 text-blue-600"></i>
                    Seleccionar Plan de Suscripción
                </h3>
                <button id="btnCerrarModalPlan1" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            {{-- Content --}}
            <div class="px-6 py-4">
                {{-- Selección de Plan --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Selecciona un plan:</label>
                    <div class="grid gap-4 md:grid-cols-3" id="planesModalContainer">
                        {{-- Los planes se cargarán dinámicamente --}}
                    </div>
                </div>

                {{-- Información del Plan Seleccionado --}}
                <div id="planSeleccionadoInfo" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="font-medium text-blue-900 mb-2">Plan Seleccionado:</h4>
                    <div class="text-sm text-blue-800">
                        <p><span class="font-medium">Nombre:</span> <span id="planNombre">-</span></p>
                        <p><span class="font-medium">Precio:</span> S/ <span id="planPrecio">0.00</span> / <span id="planFrecuencia">mes</span></p>
                        <p><span class="font-medium">Descripción:</span> <span id="planDescripcion">-</span></p>
                    </div>
                </div>

                {{-- Términos y Condiciones --}}
                <div class="mb-6">
                    <label class="flex items-start">
                        <input type="checkbox" id="aceptarTerminos" class="mt-1 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">
                            Acepto los <a href="#" class="text-blue-600 hover:text-blue-800">términos y condiciones</a> 
                            del servicio. El pago se procesará de forma segura a través de MercadoPago.
                        </span>
                    </label>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 space-x-3">
                <button id="btnCerrarModalPlan2"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button id="btnProcesarSuscripcion"
                        disabled
                        class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed">
                    <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                    Pagar con MercadoPago
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Los scripts para manejar este modal están en suscripcion-modales.js --}}
