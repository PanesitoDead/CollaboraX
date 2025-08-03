{{-- Información de Suscripción y Pagos --}}
<div class="space-y-6">
    {{-- Estado de la Suscripción Actual --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i data-lucide="credit-card" class="h-8 w-8 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        @if(isset($tieneSuscripcionActiva) && $tieneSuscripcionActiva && isset($suscripcionActual) && $suscripcionActual)
                            @if(isset($suscripcionActual['plan_nombre']))
                                Plan {{ $suscripcionActual['plan_nombre'] }}
                            @elseif(isset($suscripcionActual['plan']) && isset($suscripcionActual['plan']['nombre']))
                                Plan {{ $suscripcionActual['plan']['nombre'] }}
                            @else
                                Plan Activo
                            @endif
                        @else
                            Sin Plan Activo
                        @endif
                    </h3>
                    <div class="mt-1 space-y-1">
                        @if(isset($tieneSuscripcionActiva) && $tieneSuscripcionActiva && isset($suscripcionActual) && $suscripcionActual)
                            {{-- Mostrar información de suscripción activa --}}
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Estado:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                    Activa
                                </span>
                            </p>
                            
                            @if(isset($diasRestantes) && $diasRestantes >= 0)
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Tiempo restante:</span> 
                                <span class="@if($diasRestantes <= 7) text-orange-600 font-medium @else text-gray-900 @endif">
                                    @if($diasRestantes == 0)
                                        Vence hoy
                                    @elseif($diasRestantes == 1)
                                        1 día restante
                                    @else
                                        {{ $diasRestantes }} días restantes
                                    @endif
                                </span>
                            </p>
                            @endif
                            
                            @if(isset($suscripcionActual['fecha_inicio']) && isset($suscripcionActual['fecha_fin']))
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Vigencia:</span> 
                                {{ date('d/m/Y', strtotime($suscripcionActual['fecha_inicio'])) }} - 
                                {{ date('d/m/Y', strtotime($suscripcionActual['fecha_fin'])) }}
                            </p>
                            @endif
                            
                            @if(isset($suscripcionActual['renovacion_automatica']))
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Renovación automática:</span> 
                                <span class="@if($suscripcionActual['renovacion_automatica']) text-green-600 @else text-orange-600 @endif">
                                    {{ $suscripcionActual['renovacion_automatica'] ? 'Activada' : 'Desactivada' }}
                                </span>
                            </p>
                            @endif
                        @else
                            {{-- No hay suscripción activa --}}
                            <p class="text-sm text-gray-600">No tienes una suscripción activa. Selecciona un plan para comenzar.</p>
                            @if(isset($planesDisponibles) && is_array($planesDisponibles) && count($planesDisponibles) > 0)
                                <p class="text-sm text-blue-600 mt-2">
                                    <i data-lucide="info" class="inline w-4 h-4 mr-1"></i>
                                    Hay {{ count($planesDisponibles) }} planes disponibles para seleccionar.
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                @if(!isset($tieneSuscripcionActiva) || !$tieneSuscripcionActiva)
                    {{-- No hay suscripción activa - El botón principal está abajo en la sección de planes --}}
                    <div class="text-sm text-gray-600">
                        <i data-lucide="info" class="inline w-4 h-4 mr-1"></i>
                        Selecciona un plan de suscripción para comenzar
                    </div>
                @else
                    {{-- Hay suscripción activa - Mostrar opciones de gestión --}}
                    <button id="btnCambiarPlan" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i data-lucide="refresh-cw" class="h-4 w-4 mr-2"></i>
                        Cambiar
                    </button>
                    
                    {{-- Switch de renovación automática --}}
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   id="autoRenovacionToggle"
                                   @if(isset($suscripcionActual['renovacion_automatica']) && $suscripcionActual['renovacion_automatica']) checked @endif
                                   class="sr-only">
                            <div class="relative">
                                <div class="block bg-gray-300 w-10 h-6 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                            </div>
                            <span class="ml-2 text-sm text-gray-700">Auto-renovar</span>
                        </label>
                    </div>
                    
                    <button id="btnCancelarSuscripcion" 
                            class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                        <i data-lucide="x-circle" class="h-4 w-4 mr-2"></i>
                        Cancelar
                    </button>
                @endif
            </div>
        </div>
        
        {{-- Alerta de vencimiento próximo --}}
        @if(isset($tieneSuscripcionActiva) && $tieneSuscripcionActiva && isset($diasRestantes) && $diasRestantes <= 7 && $diasRestantes >= 0)
        <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg">
            <div class="flex items-center">
                <i data-lucide="alert-triangle" class="h-5 w-5 text-orange-600 mr-2"></i>
                <div class="text-sm">
                    <span class="font-medium text-orange-800">
                        @if($diasRestantes == 0)
                            Tu suscripción vence hoy.
                        @elseif($diasRestantes <= 3)
                            Tu suscripción vence en {{ $diasRestantes }} {{ $diasRestantes == 1 ? 'día' : 'días' }}.
                        @else
                            Tu suscripción vence pronto ({{ $diasRestantes }} días).
                        @endif
                    </span>
                    <span class="text-orange-700">
                        @if(!isset($suscripcionActual['renovacion_automatica']) || !$suscripcionActual['renovacion_automatica'])
                            Considera renovar tu plan para evitar interrupciones.
                        @else
                            Se renovará automáticamente.
                        @endif
                    </span>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Planes Disponibles - Solo mostrar si no tiene suscripción activa --}}
    @if(!isset($tieneSuscripcionActiva) || !$tieneSuscripcionActiva)
        @if(isset($planesDisponibles) && is_array($planesDisponibles) && count($planesDisponibles) > 0)
        <div>
            <h4 class="text-lg font-medium text-gray-900 mb-4">Planes Disponibles</h4>
            <p class="text-sm text-gray-600 mb-4">
                <i data-lucide="info" class="h-4 w-4 inline mr-1"></i>
                Haz clic en "Suscribirse" para ver todos los planes disponibles y seleccionar el que más te convenga.
            </p>
            <div class="text-center">
                <button id="btnSuscribirse" 
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                    <i data-lucide="credit-card" class="h-5 w-5 mr-2"></i>
                    Suscribirse a un Plan
                </button>
            </div>
            <div class="grid gap-4 md:grid-cols-3 mt-6">
                @foreach($planesDisponibles as $plan)
                    @if(is_array($plan) && isset($plan['nombre']) && isset($plan['precio']))
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="text-center">
                            <h5 class="text-lg font-medium text-gray-900">{{ $plan['nombre'] }}</h5>
                            <div class="mt-2">
                                <span class="text-2xl font-bold text-gray-900">S/ {{ number_format($plan['precio'], 2) }}</span>
                                <span class="text-sm text-gray-500">/ {{ $plan['frecuencia'] ?? 'mes' }}</span>
                            </div>
                            @if(isset($plan['descripcion']) && $plan['descripcion'])
                                <p class="mt-2 text-sm text-gray-600">{{ $plan['descripcion'] }}</p>
                            @endif
                            <div class="mt-4">
                                <div class="text-center">
                                    <span class="text-xs text-gray-500">Disponible para suscripción</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @else
        <div class="text-center py-8">
            <i data-lucide="alert-circle" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
            <h4 class="text-lg font-medium text-gray-900 mb-2">No hay planes disponibles</h4>
            <p class="text-gray-600">Los planes de suscripción no están disponibles en este momento. Por favor intenta más tarde.</p>
        </div>
        @endif
    @endif

    {{-- Historial de Pagos --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-medium text-gray-900">Historial de Pagos</h4>
            <div class="flex gap-2">
                <button id="btnActualizarHistorial" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i data-lucide="refresh-cw" class="h-4 w-4 mr-2"></i>
                    Actualizar
                </button>
            </div>
        </div>

        {{-- Información del Estado --}}
        <div class="mb-4">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                <div class="text-sm text-gray-600">
                    <span class="font-medium">Total de pagos en historial:</span> 
                    <span class="text-gray-900">{{ isset($historialPagos) ? count($historialPagos) : 0 }}</span>
                    @if(isset($tieneSuscripcionActiva))
                        | <span class="font-medium">Suscripción activa:</span> 
                        <span class="@if($tieneSuscripcionActiva) text-green-600 @else text-red-600 @endif">
                            {{ $tieneSuscripcionActiva ? 'Sí' : 'No' }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tabla de Pagos --}}
        @include('partials.admin.tablas.pag.pagos-tabla-pag')
    </div>
</div>
