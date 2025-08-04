@extends('layouts.private.admin')

@section('title', 'Configuración')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/configuracion.css') }}">
@endpush

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Configuración</h1>
            <p class="text-gray-600">Ajustes generales del sistema</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-lg border border-gray-300">
        <!-- Tabs -->
        <nav class="border-b border-gray-200">
            <div class="flex space-x-8 px-6">
                <button
                    type="button"
                    data-tab="suscripcion"
                    class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-transparent py-4 px-1 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition"
                    onclick="showTab('suscripcion')"
                >
                    <i data-lucide="credit-card" class="w-4 h-4 mr-1"></i>
                    Suscripción y Pagos
                </button>
                <button
                    type="button"
                    data-tab="empresa"
                    class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-blue-500 py-4 px-1 font-medium text-sm text-blue-600 transition"
                    onclick="showTab('empresa')"
                >
                    <i data-lucide="briefcase" class="w-4 h-4 mr-1"></i>
                    Empresa
                </button>
                <button
                    type="button"
                    data-tab="password"
                    class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-transparent py-4 px-1 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition"
                    onclick="showTab('password')"
                >
                    <i data-lucide="lock" class="w-4 h-4 mr-1"></i>
                    Contraseña
                </button>
            </div>
        </nav>

        <!-- Tab Contents -->
        <div id="tab-suscripcion" class="tab-content p-6 hidden">
            @include('partials.admin.suscripcion.suscripcion-info')
        </div>

        <div id="tab-empresa" class="tab-content p-6">
            <form method="POST" action="{{ route('admin.configuracion.update-empresa') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Datos básicos --}}
                @include('partials.admin.empresa.datos-basicos')

                {{-- Datos de contacto --}}
                @include('partials.admin.empresa.datos-contacto')

                {{-- Configuración adicional --}}
                @include('partials.admin.empresa.configuracion-adicional')

                {{-- Botones de acción --}}
                @include('partials.admin.empresa.botones-accion')

            </form>
        </div>

        <div id="tab-password" class="tab-content p-6 hidden">
            @include('partials.admin.password.cambiar-password')
        </div>
    </div>
</div>

{{-- Modales fuera de los tabs para que sean accesibles --}}
@include('partials.admin.modales.suscripcion.seleccionar-plan-modal')
@include('partials.admin.modales.suscripcion.estado-pago-modal')

@endsection

@push('scripts')
<!-- Variables globales para JavaScript -->
<script>
// Variable global para el ID de suscripción actual
window.suscripcionActualId = {{ isset($suscripcionActual) && $suscripcionActual && isset($suscripcionActual['id']) ? $suscripcionActual['id'] : 'null' }};

// Función para cambiar renovación automática
async function cambiarRenovacionAutomatica(suscripcionId, renovacionAutomatica) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    if (!csrfToken) {
        throw new Error('Token CSRF no encontrado');
    }
    
    const response = await fetch('/admin/suscripciones/cambiar-renovacion', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        },
        body: JSON.stringify({
            suscripcion_id: suscripcionId,
            renovacion_automatica: renovacionAutomatica
        })
    });
    
    const data = await response.json();
    
    if (!response.ok) {
        throw new Error(data.message || 'Error en la petición');
    }
    
    return data;
}

// Función para limpiar cache del usuario
async function limpiarCacheUsuario() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    if (!csrfToken) {
        throw new Error('Token CSRF no encontrado');
    }
    
    const response = await fetch('/admin/suscripciones/limpiar-cache', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        }
    });
    
    const data = await response.json();
    
    if (!response.ok) {
        throw new Error(data.message || 'Error al limpiar cache');
    }
    
    return data;
}

// Función para actualizar datos de suscripción
async function actualizarDatosSuscripcion() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    if (!csrfToken) {
        throw new Error('Token CSRF no encontrado');
    }
    
    const response = await fetch('/admin/suscripciones/obtener-resumen-completo?forzar_actualizacion=true', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
        }
    });
    
    const data = await response.json();
    
    if (!response.ok) {
        throw new Error(data.message || 'Error al obtener datos');
    }
    
    return data;
}

// Función para actualizar la UI con nuevos datos sin recargar la página
function actualizarUIConNuevosDatos(datos) {
    const suscripcionData = datos.data;
    
    // Actualizar variable global
    if (suscripcionData.suscripcion_activa) {
        window.suscripcionActualId = suscripcionData.suscripcion_activa.id;
    }
    
    // Actualizar estado de autorenovación
    const autoToggle = document.getElementById('autoRenovacionToggle');
    if (autoToggle && suscripcionData.suscripcion_activa) {
        const renovacionActiva = suscripcionData.suscripcion_activa.renovacion_automatica;
        autoToggle.checked = renovacionActiva;
        autoToggle.dataset.suscripcionId = suscripcionData.suscripcion_activa.id;
        
        // Actualizar UI del toggle
        const container = autoToggle.closest('.flex');
        const toggleBg = container.querySelector('.toggle-bg');
        const toggleDot = container.querySelector('.toggle-dot');
        
        if (renovacionActiva) {
            toggleBg.classList.remove('bg-gray-300');
            toggleBg.classList.add('bg-blue-500');
            toggleDot.classList.add('transform', 'translate-x-4');
        } else {
            toggleBg.classList.remove('bg-blue-500');
            toggleBg.classList.add('bg-gray-300');
            toggleDot.classList.remove('transform', 'translate-x-4');
        }
    }
}

function mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    
    // Aplicar clases base y específicas del tipo
    const baseClasses = 'fixed top-4 right-4 p-4 rounded-lg z-50 transition-all duration-300 max-w-sm';
    const tipoClasses = {
        'success': 'notification-success text-white',
        'error': 'notification-error text-white',
        'warning': 'notification-warning text-gray-900',
        'info': 'notification-info text-white'
    };
    
    notificacion.className = `${baseClasses} ${tipoClasses[tipo] || tipoClasses.info}`;
    
    // Agregar ícono según el tipo
    const iconos = {
        'success': 'check-circle',
        'error': 'x-circle',
        'warning': 'alert-triangle',
        'info': 'info'
    };
    
    notificacion.innerHTML = `
        <div class="flex items-center">
            <i data-lucide="${iconos[tipo] || iconos.info}" class="w-5 h-5 mr-2 flex-shrink-0"></i>
            <span class="text-sm font-medium">${mensaje}</span>
        </div>
    `;
    
    // Inicializar estado de entrada
    notificacion.style.transform = 'translateX(100%)';
    notificacion.style.opacity = '0';
    
    // Añadir a la página
    document.body.appendChild(notificacion);
    
    // Inicializar iconos de Lucide
    if (window.lucide) {
        window.lucide.createIcons();
    }
    
    // Animación de entrada
    setTimeout(() => {
        notificacion.style.transform = 'translateX(0)';
        notificacion.style.opacity = '1';
    }, 100);
    
    // Remover después de 5 segundos
    setTimeout(() => {
        notificacion.style.transform = 'translateX(100%)';
        notificacion.style.opacity = '0';
        setTimeout(() => {
            if (document.body.contains(notificacion)) {
                document.body.removeChild(notificacion);
            }
        }, 300);
    }, 5000);
}

// Función para mostrar tabs
function showTab(tabName) {
    // Ocultar todos los contenidos
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remover estilos activos de todos los botones
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Mostrar contenido activo
    document.getElementById(`tab-${tabName}`).classList.remove('hidden');
    
    // Activar botón correspondiente
    document.querySelector(`[data-tab="${tabName}"]`).classList.remove('border-transparent', 'text-gray-500');
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('border-blue-500', 'text-blue-600');
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si venimos de un pago exitoso
    const urlParams = new URLSearchParams(window.location.search);
    const pagoExitoso = urlParams.get('pago_exitoso');
    const collection_status = urlParams.get('collection_status');
    
    // Si detectamos que se completó un pago, actualizar automáticamente los datos
    if (pagoExitoso === 'true' || collection_status === 'approved') {
        console.log('Pago exitoso detectado, actualizando datos...');
        
        // Mostrar notificación de pago exitoso
        mostrarNotificacion('Pago procesado exitosamente. Actualizando datos...', 'success');
        
        // Limpiar cache y esperar un momento para que el sistema procese
        setTimeout(() => {
            // Primero limpiar cache
            limpiarCacheUsuario()
                .then(() => {
                    console.log('Cache limpiado, obteniendo datos frescos...');
                    // Luego obtener datos frescos
                    return actualizarDatosSuscripcion();
                })
                .then(() => {
                    mostrarNotificacion('Suscripción actualizada correctamente', 'success');
                    // Limpiar parámetros de la URL sin recargar
                    const newUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, newUrl);
                    
                    // Recargar la página para mostrar todos los cambios
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                })
                .catch(error => {
                    console.error('Error actualizando datos:', error);
                    mostrarNotificacion('Pago exitoso, pero hubo un error actualizando los datos. Por favor, actualiza la página.', 'warning');
                    
                    // En caso de error, forzar recarga después de un momento
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                });
        }, 2000); // Esperar 2 segundos para dar tiempo al procesamiento del pago
    }
    
    // Verificar si hay errores en la sesión para mostrar notificaciones
    @if(session('success'))
        mostrarNotificacion('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        mostrarNotificacion('{{ session('error') }}', 'error');
    @endif
    
    // Verificar si hay errores de validación y mostrar el tab correspondiente
    @if($errors->any())
        @php
            $hasPasswordErrors = $errors->has('current_password') || 
                                $errors->has('new_password') || 
                                $errors->has('new_password_confirmation');
        @endphp
        
        @if($hasPasswordErrors)
            // Si hay errores de contraseña, mostrar el tab de contraseña
            showTab('password');
        @else
            // Si hay otros errores, mostrar el tab de empresa
            showTab('empresa');
        @endif
        
        // Mostrar notificación sobre los errores
        mostrarNotificacion('Por favor, corrija los errores en el formulario.', 'error');
    @endif
    
    // Toggle switches - Actualizado para manejar autorenovación
    document.querySelectorAll('.toggle-switch').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const bg = this.querySelector('.toggle-bg');
            const dot = this.querySelector('.toggle-dot');
            const isActive = bg.classList.contains('bg-blue-500');
            
            if (!isActive) {
                dot.style.transform = 'translateX(1rem)';
                bg.classList.remove('bg-gray-300');
                bg.classList.add('bg-blue-500');
            } else {
                dot.style.transform = 'translateX(0)';
                bg.classList.remove('bg-blue-500');
                bg.classList.add('bg-gray-300');
            }
        });
    });
    
    // Event listener específico para el toggle de autorenovación
    const autoRenovacionToggle = document.getElementById('autoRenovacionToggle');
    if (autoRenovacionToggle) {
        autoRenovacionToggle.addEventListener('change', function() {
            const suscripcionId = this.dataset.suscripcionId;
            const isChecked = this.checked;
            
            if (!suscripcionId) {
                mostrarNotificacion('No se encontró ID de suscripción', 'error');
                // Revertir el toggle
                this.checked = !isChecked;
                return;
            }
            
            // Mostrar estado de carga
            const container = this.closest('.flex');
            const loadingSpinner = document.createElement('div');
            loadingSpinner.className = 'ml-2 w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin';
            loadingSpinner.id = 'autorenovar-loading';
            container.appendChild(loadingSpinner);
            
            // Deshabilitar el toggle temporalmente
            this.disabled = true;
            
            // Hacer la petición AJAX
            cambiarRenovacionAutomatica(suscripcionId, isChecked)
                .then(response => {
                    if (response.success) {
                        mostrarNotificacion(response.message || 'Configuración actualizada correctamente', 'success');
                        
                        // Actualizar la UI del toggle
                        const toggleBg = container.querySelector('.toggle-bg');
                        const toggleDot = container.querySelector('.toggle-dot');
                        
                        if (isChecked) {
                            toggleBg.classList.remove('bg-gray-300');
                            toggleBg.classList.add('bg-blue-500');
                            toggleDot.classList.add('transform', 'translate-x-4');
                        } else {
                            toggleBg.classList.remove('bg-blue-500');
                            toggleBg.classList.add('bg-gray-300');
                            toggleDot.classList.remove('transform', 'translate-x-4');
                        }
                    } else {
                        mostrarNotificacion(response.message || 'Error al actualizar configuración', 'error');
                        // Revertir el toggle
                        this.checked = !isChecked;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarNotificacion('Error de conexión', 'error');
                    // Revertir el toggle
                    this.checked = !isChecked;
                })
                .finally(() => {
                    // Remover spinner y rehabilitar toggle
                    const spinner = document.getElementById('autorenovar-loading');
                    if (spinner) spinner.remove();
                    this.disabled = false;
                });
        });
    }
    
    // Event listener para el botón de actualizar datos
    const btnActualizarDatos = document.getElementById('btnActualizarDatos');
    if (btnActualizarDatos) {
        btnActualizarDatos.addEventListener('click', function() {
            // Mostrar estado de carga
            const originalText = this.innerHTML;
            this.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 mr-2 animate-spin"></i>Actualizando...';
            this.disabled = true;
            
            // Forzar actualización de datos directamente
            mostrarNotificacion('Iniciando actualización de datos...', 'info');
            
            actualizarDatosSuscripcion()
                .then((response) => {
                    mostrarNotificacion('Datos actualizados correctamente', 'success');
                    
                    // Intentar actualizar la UI sin recargar
                    try {
                        actualizarUIConNuevosDatos(response);
                        mostrarNotificacion('Interfaz actualizada - Recargando página...', 'success');
                        
                        // Recargar automáticamente después de 1.5 segundos para mostrar todos los cambios
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } catch (uiError) {
                        console.error('Error actualizando UI:', uiError);
                        mostrarNotificacion('Recargando página para mostrar los cambios...', 'info');
                        // Si falla la actualización de UI, recargar la página
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarNotificacion('Error al actualizar datos', 'error');
                })
                .finally(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                    // Re-inicializar los iconos de Lucide
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
        });
    }
});
</script>

<!-- Scripts organizados por funcionalidad -->
<script src="{{ asset('js/configuracion-tabs.js') }}"></script>
<script src="{{ asset('js/suscripcion-modales.js') }}"></script>
<script src="{{ asset('js/suscripcion-funciones.js') }}"></script>
<script src="{{ asset('js/configuracion-init.js') }}"></script>
@endpush