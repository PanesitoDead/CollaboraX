/**
 * Gestión de modales para suscripciones
 */

// Variable global para el plan seleccionado
let planSeleccionado = null;
// Variable para controlar si los planes ya están cargados
let planesYaCargados = false;
// Array para manejar timers y prevenir memory leaks
window.modalTimers = [];

// Función para actualizar historial de pagos
window.actualizarHistorialPagos = function() {
    location.reload();
};

// Función temporal para probar la conexión a crear-preferencia
window.testConexionMercadoPago = function() {
    console.log('=== TEST CONEXIÓN MERCADOPAGO ===');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('Token CSRF no encontrado');
        alert('Error: Token CSRF no encontrado');
        return;
    }
    
    const testData = {
        plan_id: 1 // Plan de prueba
    };
    
    console.log('Enviando test a crear-preferencia...');
    
    fetch('/admin/suscripciones/crear-preferencia', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        },
        body: JSON.stringify(testData)
    })
    .then(response => {
        console.log('Respuesta del test:', {
            status: response.status,
            statusText: response.statusText,
            ok: response.ok
        });
        return response.text();
    })
    .then(text => {
        console.log('Respuesta texto:', text);
        try {
            const data = JSON.parse(text);
            console.log('Respuesta JSON:', data);
            alert('Test exitoso: ' + JSON.stringify(data, null, 2));
        } catch (e) {
            console.log('Respuesta no es JSON válido');
            alert('Respuesta del servidor: ' + text);
        }
    })
    .catch(error => {
        console.error('Error en test:', error);
        alert('Error: ' + error.message);
    });
};

// Función para resetear el estado de carga de planes
window.resetPlanesCache = function() {
    planesYaCargados = false;
    console.log('Cache de planes reseteado');
};

// Función de debugging para verificar suscripción
window.debugSuscripcion = function() {
    console.log('=== DEBUG SUSCRIPCIÓN ===');
    console.log('window.suscripcionActualId:', window.suscripcionActualId);
    console.log('Tipo:', typeof window.suscripcionActualId);
    console.log('Es null:', window.suscripcionActualId === null);
    console.log('Es undefined:', window.suscripcionActualId === undefined);
    console.log('Es string null:', window.suscripcionActualId === 'null');
    
    // Verificar datos en la página
    const suscripcionInfo = document.querySelector('[data-suscripcion-info]');
    if (suscripcionInfo) {
        console.log('Información de suscripción en DOM:', suscripcionInfo.dataset);
    }
    
    // Verificar si hay elementos que indiquen suscripción activa
    const btnCancelar = document.getElementById('btnCancelarSuscripcion');
    console.log('Botón cancelar existe:', !!btnCancelar);
    console.log('Botón cancelar visible:', btnCancelar ? !btnCancelar.classList.contains('hidden') : false);
};

// Función temporal para probar cancelación con ID manual
window.testCancelarConId = function(suscripcionId) {
    if (!suscripcionId) {
        suscripcionId = 4; // Usar ID de suscripción que sabemos que existe
        console.log('Usando ID de suscripción por defecto:', suscripcionId);
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    console.log('=== TEST CANCELACIÓN ===');
    console.log('ID de suscripción:', suscripcionId);
    console.log('CSRF Token disponible:', !!csrfToken);
    
    fetch('/admin/suscripciones/cancelar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
        },
        body: JSON.stringify({
            suscripcion_id: suscripcionId
        })
    })
    .then(response => {
        console.log('Respuesta HTTP:', response.status, response.statusText);
        return response.text();
    })
    .then(text => {
        console.log('Respuesta texto:', text);
        try {
            const data = JSON.parse(text);
            console.log('Respuesta JSON:', data);
            alert('Resultado: ' + JSON.stringify(data, null, 2));
        } catch (e) {
            console.error('Error al parsear JSON:', e);
            alert('Respuesta no es JSON válido: ' + text);
        }
    })
    .catch(error => {
        console.error('Error en test:', error);
        alert('Error: ' + error.message);
    });
};

// Configuración de event listeners para prevenir bucles
document.addEventListener('DOMContentLoaded', function() {
    console.log('Configurando event listeners para modales');
    
    // Event listener principal para botón de suscripción (solo hay uno ahora)
    const btnSuscribirse = document.getElementById('btnSuscribirse');
    if (btnSuscribirse) {
        btnSuscribirse.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Click en botón Suscribirse a un Plan');
            
            // Verificar si ya está procesando
            if (this.disabled || this.classList.contains('processing')) {
                console.log('Botón ya está procesando, ignorando click');
                return;
            }
            
            // Marcar como procesando temporalmente
            this.classList.add('processing');
            setTimeout(() => {
                this.classList.remove('processing');
            }, 1000);
            
            window.abrirModalSeleccionarPlan();
        });
        
        console.log('Event listener configurado para btnSuscribirse');
    } else {
        console.log('btnSuscribirse no encontrado en DOM');
    }

    // Event listener para botones de cambiar plan (con suscripción activa)
    const btnCambiarPlan = document.getElementById('btnCambiarPlan');
    if (btnCambiarPlan) {
        btnCambiarPlan.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Click en botón Cambiar Plan');
            window.abrirModalSeleccionarPlan();
        });
    }

    // Event listener para botones de cancelar suscripción
    const btnCancelarSuscripcion = document.getElementById('btnCancelarSuscripcion');
    if (btnCancelarSuscripcion) {
        btnCancelarSuscripcion.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Click en botón Cancelar Suscripción');
            window.abrirModalCancelarSuscripcion();
        });
    }
    
    // Prevenir múltiples clicks en botones de abrir modal
    const btnsAbrirModal = document.querySelectorAll('[onclick*="abrirModal"]');
    btnsAbrirModal.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Debounce para prevenir múltiples clicks
            if (this.disabled || this.classList.contains('processing')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            
            this.classList.add('processing');
            setTimeout(() => {
                this.classList.remove('processing');
            }, 1000);
        });
    });
    
    // Event listener para cerrar modales con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modalAbierto = document.querySelector('.modal:not(.hidden)');
            if (modalAbierto) {
                console.log('Cerrando modal con ESC');
                const modalId = modalAbierto.id;
                if (modalId === 'modalSeleccionarPlan') {
                    window.cerrarModalSeleccionarPlan();
                } else if (modalId === 'modalCancelarSuscripcion') {
                    window.cerrarModalCancelarSuscripcion();
                }
            }
        }
    });
    
    // Prevenir cierre automático por eventos propagados
    const modales = document.querySelectorAll('.modal');
    modales.forEach(modal => {
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
    
    console.log('Event listeners configurados correctamente');
    
    // Configurar event listeners adicionales para modales
    setupAdditionalModalListeners();
});

function setupAdditionalModalListeners() {
    // Debug: Verificar variables globales
    console.log('=== VERIFICACIÓN INICIAL ===');
    console.log('window.suscripcionActualId:', window.suscripcionActualId);
    console.log('Tipo:', typeof window.suscripcionActualId);
    console.log('CSRF Token:', !!document.querySelector('meta[name="csrf-token"]'));
    
    // Botones del modal seleccionar plan
    const btnCerrarModalPlan1 = document.getElementById('btnCerrarModalPlan1');
    const btnCerrarModalPlan2 = document.getElementById('btnCerrarModalPlan2');
    const btnProcesarSuscripcion = document.getElementById('btnProcesarSuscripcion');
    
    if (btnCerrarModalPlan1) {
        btnCerrarModalPlan1.addEventListener('click', window.cerrarModalSeleccionarPlan);
    }
    
    if (btnCerrarModalPlan2) {
        btnCerrarModalPlan2.addEventListener('click', window.cerrarModalSeleccionarPlan);
    }
    
    if (btnProcesarSuscripcion) {
        btnProcesarSuscripcion.addEventListener('click', window.procesarConMercadoPago);
    }
    
    // Botones del modal cancelar suscripción
    const btnCerrarModalCancelar1 = document.getElementById('btnCerrarModalCancelar1');
    const btnCerrarModalCancelar2 = document.getElementById('btnCerrarModalCancelar2');
    const btnConfirmarCancelacion = document.getElementById('btnConfirmarCancelacion');
    
    if (btnCerrarModalCancelar1) {
        btnCerrarModalCancelar1.addEventListener('click', window.cerrarModalCancelarSuscripcion);
    }
    
    if (btnCerrarModalCancelar2) {
        btnCerrarModalCancelar2.addEventListener('click', window.cerrarModalCancelarSuscripcion);
    }
    
    if (btnConfirmarCancelacion) {
        btnConfirmarCancelacion.addEventListener('click', window.confirmarCancelacion);
    }
    
    // Event listener para el checkbox de términos y condiciones
    const aceptarTerminos = document.getElementById('aceptarTerminos');
    if (aceptarTerminos) {
        aceptarTerminos.addEventListener('change', window.validarFormularioSuscripcion);
    }
    
    // Event listener para cerrar modal al hacer click fuera de él
    const modalSeleccionarPlan = document.getElementById('modalSeleccionarPlan');
    if (modalSeleccionarPlan) {
        modalSeleccionarPlan.addEventListener('click', function(e) {
            if (e.target === this) {
                window.cerrarModalSeleccionarPlan(e);
            }
        });
    }
    
    // Event listener para cerrar modal de cancelación al hacer click fuera de él
    const modalCancelarSuscripcion = document.getElementById('modalCancelarSuscripcion');
    if (modalCancelarSuscripcion) {
        modalCancelarSuscripcion.addEventListener('click', function(e) {
            if (e.target === this) {
                window.cerrarModalCancelarSuscripcion();
            }
        });
    }
    
    // Event listener para actualizar historial
    const btnActualizarHistorial = document.getElementById('btnActualizarHistorial');
    if (btnActualizarHistorial) {
        btnActualizarHistorial.addEventListener('click', window.actualizarHistorialPagos);
    }
    
    console.log('Event listeners adicionales configurados');
}

// ===== MODAL SELECCIONAR PLAN =====
window.abrirModalSeleccionarPlan = function() {
    console.log('Abriendo modal seleccionar plan');
    
    // Prevenir múltiples llamadas
    if (window.modalAbriendo) {
        console.log('Modal ya se está abriendo, cancelando...');
        return;
    }
    
    window.modalAbriendo = true;
    
    cargarPlanesDisponibles(() => {
        const modal = document.getElementById('modalSeleccionarPlan');
        if (!modal) {
            console.error('Modal no encontrado');
            window.modalAbriendo = false;
            return;
        }
        
        const modalContent = modal.querySelector('.modal-content');
        
        modal.classList.remove('hidden');
        modal.style.display = 'block';
        
        // Trigger animation
        setTimeout(() => {
            modalContent.style.transform = 'scale(1)';
            modalContent.style.opacity = '1';
            window.modalAbriendo = false;
            console.log('Modal seleccionar plan abierto exitosamente');
        }, 10);
    });
};

window.cerrarModalSeleccionarPlan = function(event) {
    console.log('Iniciando cierre de modal seleccionar plan');
    
    const modal = document.getElementById('modalSeleccionarPlan');
    if (!modal) {
        console.log('Modal no encontrado');
        return;
    }
    
    const modalContent = modal.querySelector('.modal-content');
    
    // Prevenir propagación de eventos si el evento está disponible
    if (event) {
        event.stopPropagation();
    }
    
    // Animación de cierre
    modalContent.style.transform = 'scale(0.9)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        limpiarSeleccionPlan();
        console.log('Modal cerrado exitosamente');
    }, 300);
}

// Función para manejar el cierre de modales de manera segura
function cerrarModalSeguro(modalId, event) {
    console.log(`Cerrando modal: ${modalId}`);
    
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.log(`Modal ${modalId} no encontrado`);
        return;
    }
    
    // Prevenir propagación de eventos durante el cierre si el evento está disponible
    if (event) {
        event.stopPropagation();
    }
    
    // Ocultar el modal
    modal.style.display = 'none';
    modal.classList.add('hidden');
    
    // Limpiar cualquier timeout o interval activo
    clearAllTimers();
    
    console.log(`Modal ${modalId} cerrado exitosamente`);
}

// Función para limpiar timers activos
function clearAllTimers() {
    if (window.modalTimers && window.modalTimers.length > 0) {
        window.modalTimers.forEach(timer => clearTimeout(timer));
        window.modalTimers = [];
    }
}

// Función mejorada para abrir modal con verificación de estado
function abrirModalSeguro(modalId) {
    console.log(`Abriendo modal: ${modalId}`);
    
    // Verificar si ya hay un modal abierto
    const modalsAbiertos = document.querySelectorAll('.modal:not(.hidden)');
    if (modalsAbiertos.length > 0) {
        console.log('Ya hay un modal abierto, cerrando primero...');
        modalsAbiertos.forEach(modal => {
            modal.style.display = 'none';
            modal.classList.add('hidden');
        });
        
        // Esperar un momento antes de abrir el nuevo modal
        setTimeout(() => {
            abrirModalInterno(modalId);
        }, 100);
    } else {
        abrirModalInterno(modalId);
    }
}

function abrirModalInterno(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.log(`Modal ${modalId} no encontrado`);
        return;
    }
    
    modal.style.display = 'block';
    modal.classList.remove('hidden');
    console.log(`Modal ${modalId} abierto exitosamente`);
}

function cargarPlanesDisponibles(callback) {
    // Si los planes ya están cargados, ejecutar callback directamente
    if (planesYaCargados) {
        console.log('Planes ya cargados, omitiendo consulta API');
        if (typeof callback === 'function') {
            callback();
        }
        return;
    }

    console.log('Cargando planes desde API...');
    // Cargar planes desde el API del microservicio
    fetch('/admin/suscripciones/planes')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('planesModalContainer');
                if (!container) {
                console.error('No se encontró el contenedor de planes');
                    return;
                }
                
                container.innerHTML = '';
                
                data.data.forEach(plan => {
                    const planCard = document.createElement('div');
                    planCard.className = 'border border-gray-200 rounded-lg p-4 cursor-pointer hover:shadow-md transition-shadow plan-card';
                    planCard.setAttribute('data-plan-id', plan.id);
                    
                    // Usar addEventListener en lugar de onclick inline
                    planCard.addEventListener('click', function() {
                        window.seleccionarPlanModal(plan);
                    });
                    
                    planCard.innerHTML = `
                        <div class="text-center">
                            <h5 class="text-lg font-medium text-gray-900">${plan.nombre || 'Sin nombre'}</h5>
                            <div class="mt-2">
                                <span class="text-2xl font-bold text-gray-900">S/ ${isNaN(parseFloat(plan.precio)) ? '0.00' : parseFloat(plan.precio).toFixed(2)}</span>
                                <span class="text-sm text-gray-500">/ ${plan.frecuencia || 'mes'}</span>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">${plan.descripcion || 'Sin descripción'}</p>
                        </div>
                    `;
                    
                    container.appendChild(planCard);
                });
                
                // Marcar como cargados
                planesYaCargados = true;
                console.log('Planes cargados exitosamente');
                
                // Ejecutar callback si se proporcionó
                if (typeof callback === 'function') {
                    callback();
                }
            } else {
                cargarPlanesDefault(callback);
            }
        })
        .catch(error => {
            console.error('Error al cargar planes:', error);
            cargarPlanesDefault(callback);
        });
}

function cargarPlanesDefault(callback) {
    // Solo cargar si no están ya cargados
    if (planesYaCargados) {
        console.log('Planes por defecto ya cargados, omitiendo...');
        if (typeof callback === 'function') {
            callback();
        }
        return;
    }

    console.log('Cargando planes por defecto...');
    // Planes por defecto en caso de error
    const planesDefault = [
        { id: 1, nombre: 'Plan Standard', precio: 29.90, frecuencia: 'mensual', descripcion: 'Ideal para uso básico' },
        { id: 2, nombre: 'Plan Business', precio: 59.90, frecuencia: 'mensual', descripcion: 'Para pequeñas empresas' },
        { id: 3, nombre: 'Plan Enterprise', precio: 99.90, frecuencia: 'mensual', descripcion: 'Para organizaciones grandes' }
    ];
    
    const container = document.getElementById('planesModalContainer');
    if (!container) {
        return;
    }
    
    container.innerHTML = '';
    
    planesDefault.forEach(plan => {
        const planCard = document.createElement('div');
        planCard.className = 'border border-gray-200 rounded-lg p-4 cursor-pointer hover:shadow-md transition-shadow plan-card';
        planCard.setAttribute('data-plan-id', plan.id);
        
        // Usar addEventListener en lugar de onclick inline
        planCard.addEventListener('click', function() {
            window.seleccionarPlanModal(plan);
        });
        
        planCard.innerHTML = `
            <div class="text-center">
                <h5 class="text-lg font-medium text-gray-900">${plan.nombre || 'Sin nombre'}</h5>
                <div class="mt-2">
                    <span class="text-2xl font-bold text-gray-900">S/ ${isNaN(parseFloat(plan.precio)) ? '0.00' : parseFloat(plan.precio).toFixed(2)}</span>
                    <span class="text-sm text-gray-500">/ ${plan.frecuencia || 'mes'}</span>
                </div>
                <p class="mt-2 text-sm text-gray-600">${plan.descripcion || 'Sin descripción'}</p>
            </div>
        `;
        
        container.appendChild(planCard);
    });
    
    // Marcar como cargados
    planesYaCargados = true;
    console.log('Planes por defecto cargados');
    
    // Ejecutar callback si se proporcionó
    if (typeof callback === 'function') {
        callback();
    }
}

window.seleccionarPlanModal = function(plan) {
    // Limpiar selección anterior
    document.querySelectorAll('.plan-card').forEach(card => {
        card.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50');
    });
    
    // Marcar plan seleccionado
    const selectedCard = document.querySelector(`[data-plan-id="${plan.id}"]`);
    selectedCard.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50');
    
    // Actualizar información del plan
    planSeleccionado = plan;
    document.getElementById('planNombre').textContent = plan.nombre || 'Sin nombre';
    
    // Validar y convertir precio
    const precio = parseFloat(plan.precio);
    document.getElementById('planPrecio').textContent = isNaN(precio) ? '0.00' : precio.toFixed(2);
    
    document.getElementById('planFrecuencia').textContent = plan.frecuencia || 'mes';
    document.getElementById('planDescripcion').textContent = plan.descripcion || 'Sin descripción';
    
    document.getElementById('planSeleccionadoInfo').classList.remove('hidden');
    window.validarFormularioSuscripcion();
}

window.validarFormularioSuscripcion = function() {
    const planSeleccionadoValido = planSeleccionado !== null;
    const terminosAceptados = document.getElementById('aceptarTerminos').checked;
    const boton = document.getElementById('btnProcesarSuscripcion');
    
    if (planSeleccionadoValido && terminosAceptados) {
        boton.disabled = false;
    } else {
        boton.disabled = true;
    }
}

function limpiarSeleccionPlan() {
    planSeleccionado = null;
    document.getElementById('planSeleccionadoInfo').classList.add('hidden');
    document.getElementById('aceptarTerminos').checked = false;
    document.getElementById('btnProcesarSuscripcion').disabled = true;
}

window.procesarConMercadoPago = function() {
    console.log('=== INICIANDO PROCESO DE PAGO ===');
    
    if (!planSeleccionado) {
        alert('Por favor selecciona un plan');
        return;
    }
    
    console.log('Plan seleccionado:', planSeleccionado);
    
    // Verificar que el token CSRF esté disponible
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('Token CSRF no encontrado');
        alert('Error: Token de seguridad no encontrado');
        return;
    }
    
    console.log('Token CSRF encontrado:', csrfToken.getAttribute('content').substring(0, 10) + '...');
    
    // Mostrar loading
    const boton = document.getElementById('btnProcesarSuscripcion');
    if (!boton) {
        console.error('Botón procesar suscripción no encontrado');
        return;
    }
    
    const textoOriginal = boton.innerHTML;
    boton.disabled = true;
    boton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Procesando...';
    
    const requestData = {
        plan_id: planSeleccionado.id
    };
    
    console.log('Enviando datos:', requestData);
    console.log('URL objetivo:', '/admin/suscripciones/crear-preferencia');
    
    // Crear preferencia de pago en MercadoPago
    fetch('/admin/suscripciones/crear-preferencia', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log('Respuesta HTTP recibida:', {
            status: response.status,
            statusText: response.statusText,
            ok: response.ok,
            url: response.url,
            headers: {
                'content-type': response.headers.get('content-type')
            }
        });
        
        if (!response.ok) {
            // Intentar obtener el texto de error del servidor
            return response.text().then(errorText => {
                console.error('Error del servidor (texto):', errorText);
                throw new Error(`Error HTTP ${response.status}: ${response.statusText} - ${errorText}`);
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Datos de respuesta exitosa:', data);
        
        boton.disabled = false;
        boton.innerHTML = textoOriginal;
        
        if (data.success && data.data && data.data.init_point) {
            console.log('Redirigiendo a MercadoPago:', data.data.init_point);
            // Cerrar modal y redirigir a MercadoPago
            window.cerrarModalSeleccionarPlan();
            window.open(data.data.init_point, '_blank');
        } else {
            console.error('Respuesta sin init_point:', data);
            const errorMsg = data.message || data.error || 'Error al crear la preferencia de pago';
            alert(errorMsg);
        }
    })
    .catch(error => {
        console.error('Error completo en fetch:', error);
        console.error('Stack trace:', error.stack);
        
        boton.disabled = false;
        boton.innerHTML = textoOriginal;
        
        let errorMessage = 'Error de conexión al procesar el pago';
        
        if (error.message.includes('Failed to fetch')) {
            errorMessage = 'No se pudo conectar con el servidor. Verifica que el servidor esté funcionando y que la URL sea correcta.';
        } else if (error.message.includes('NetworkError')) {
            errorMessage = 'Error de red. Verifica tu conexión a internet.';
        } else if (error.message.includes('HTTP')) {
            errorMessage = `Error del servidor: ${error.message}`;
        } else {
            errorMessage = `Error: ${error.message}`;
        }
        
        console.error('Mensaje de error final:', errorMessage);
        alert(errorMessage);
    });
}

// Función para abrir el modal desde el botón "Cancelar" de la sección principal
window.confirmarCancelarSuscripcion = function() {
    window.abrirModalCancelarSuscripcion();
}

// Función para configurar event listeners en modales reubicados
function configurarEventListenersModal(modal) {
    // Botones de cerrar y confirmar
    const btnCerrarModalCancelar1 = modal.querySelector('#btnCerrarModalCancelar1');
    const btnCerrarModalCancelar2 = modal.querySelector('#btnCerrarModalCancelar2');
    const btnConfirmarCancelacion = modal.querySelector('#btnConfirmarCancelacion');
    
    if (btnCerrarModalCancelar1) {
        btnCerrarModalCancelar1.addEventListener('click', window.cerrarModalCancelarSuscripcion);
    }
    
    if (btnCerrarModalCancelar2) {
        btnCerrarModalCancelar2.addEventListener('click', window.cerrarModalCancelarSuscripcion);
    }
    
    if (btnConfirmarCancelacion) {
        btnConfirmarCancelacion.addEventListener('click', window.confirmarCancelacion);
    }
    
    // Click fuera del modal
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            window.cerrarModalCancelarSuscripcion();
        }
    });
}

// ===== MODAL CANCELAR SUSCRIPCIÓN =====
window.abrirModalCancelarSuscripcion = function() {
    const modal = document.getElementById('modalCancelarSuscripcion');
    
    if (!modal) {
        alert('Error: Modal de cancelación no encontrado');
        return;
    }
    
    // Si el parent es otro modal, MOVERLO AUTOMÁTICAMENTE
    if (modal.parentElement?.id && modal.parentElement.id.includes('modal')) {
        // MOVER EL MODAL AL BODY DIRECTAMENTE
        const modalClone = modal.cloneNode(true);
        document.body.appendChild(modalClone);
        
        // Eliminar el modal original anidado
        modal.remove();
        
        // Usar el nuevo modal
        const newModal = document.getElementById('modalCancelarSuscripcion');
        newModal.classList.remove('hidden');
        
        const modalContent = newModal.querySelector('.modal-content');
        if (modalContent) {
            setTimeout(() => {
                modalContent.style.transform = 'scale(1)';
                modalContent.style.opacity = '1';
            }, 50);
        }
        
        // Reconfigurar event listeners para el nuevo modal
        configurarEventListenersModal(newModal);
        return;
    }
    
    // CONTINUAR NORMALMENTE
    modal.classList.remove('hidden');
    
    const modalContent = modal.querySelector('.modal-content');
    if (!modalContent) {
        return;
    }
    
    // Aplicar animación
    setTimeout(() => {
        modalContent.style.transform = 'scale(1)';
        modalContent.style.opacity = '1';
    }, 50);
}

window.cerrarModalCancelarSuscripcion = function() {
    const modal = document.getElementById('modalCancelarSuscripcion');
    const modalContent = modal.querySelector('.modal-content');
    
    modalContent.style.transform = 'scale(0.9)';
    modalContent.style.opacity = '0';
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

window.confirmarCancelacion = function() {
    console.log('=== INICIANDO PROCESO DE CANCELACIÓN ===');
    console.log('window.suscripcionActualId:', window.suscripcionActualId);
    console.log('Tipo:', typeof window.suscripcionActualId);
    
    // 1. PRIMERO INTENTAR OBTENER LA SUSCRIPCIÓN ACTIVA DESDE EL SERVIDOR
    console.log('Intentando obtener suscripción activa desde servidor...');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    // Obtener la suscripción activa del usuario autenticado
    fetch('/admin/suscripciones/actual', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
        }
    })
    .then(response => {
        console.log('Respuesta de suscripción actual:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Datos de suscripción actual:', data);
        
        let suscripcionId = null;
        
        // Extraer ID de suscripción de la respuesta
        if (data.success && data.data && data.data.id) {
            suscripcionId = data.data.id;
        } else if (data.data && data.data.suscripcion_activa && data.data.suscripcion_activa.id) {
            suscripcionId = data.data.suscripcion_activa.id;
        } else if (window.suscripcionActualId && window.suscripcionActualId !== 'null' && window.suscripcionActualId !== null) {
            suscripcionId = window.suscripcionActualId;
        }
        
        console.log('ID de suscripción para cancelar:', suscripcionId);
        
        if (!suscripcionId) {
            alert('No se encontró suscripción activa para cancelar');
            return;
        }
        
        // 2. PROCEDER CON LA CANCELACIÓN
        const boton = document.getElementById('btnConfirmarCancelacion');
        if (!boton) {
            console.error('Botón de confirmación no encontrado');
            return;
        }
        
        const textoOriginal = boton.innerHTML;
        
        boton.disabled = true;
        boton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Cancelando...';
        
        console.log('Enviando solicitud de cancelación con ID:', suscripcionId);
        
        fetch('/admin/suscripciones/cancelar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
            },
            body: JSON.stringify({
                suscripcion_id: suscripcionId
            })
        })
        .then(response => {
            console.log('Respuesta de cancelación:', {
                status: response.status,
                statusText: response.statusText,
                ok: response.ok
            });
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Error del servidor:', text);
                    throw new Error(`Error HTTP: ${response.status} - ${text}`);
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Resultado de cancelación:', data);
            
            boton.disabled = false;
            boton.innerHTML = textoOriginal;
            
            if (data.success) {
                alert('Suscripción cancelada exitosamente');
                window.cerrarModalCancelarSuscripcion();
                location.reload();
            } else {
                alert(data.message || 'Error al cancelar la suscripción');
            }
        })
        .catch(error => {
            console.error('Error al cancelar:', error);
            
            boton.disabled = false;
            boton.innerHTML = textoOriginal;
            alert('Error al cancelar la suscripción: ' + error.message);
        });
    })
    .catch(error => {
        console.error('Error al obtener suscripción actual:', error);
        
        // Fallback: usar window.suscripcionActualId si está disponible
        if (window.suscripcionActualId && window.suscripcionActualId !== 'null' && window.suscripcionActualId !== null) {
            console.log('Usando fallback con window.suscripcionActualId:', window.suscripcionActualId);
            procederConCancelacion(window.suscripcionActualId);
        } else {
            alert('No se pudo obtener información de la suscripción activa');
        }
    });
}

// Función auxiliar para procesar cancelación con ID conocido
function procederConCancelacion(suscripcionId) {
    const boton = document.getElementById('btnConfirmarCancelacion');
    if (!boton) return;
    
    const textoOriginal = boton.innerHTML;
    boton.disabled = true;
    boton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Cancelando...';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    fetch('/admin/suscripciones/cancelar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
        },
        body: JSON.stringify({
            suscripcion_id: suscripcionId
        })
    })
    .then(response => response.json())
    .then(data => {
        boton.disabled = false;
        boton.innerHTML = textoOriginal;
        
        if (data.success) {
            alert('Suscripción cancelada exitosamente');
            window.cerrarModalCancelarSuscripcion();
            location.reload();
        } else {
            alert(data.message || 'Error al cancelar la suscripción');
        }
    })
    .catch(error => {
        boton.disabled = false;
        boton.innerHTML = textoOriginal;
        alert('Error: ' + error.message);
    });
}

// ===== MODAL ESTADO PAGO =====
window.mostrarEstadoPago = function(pagoId, estado, detalles) {
    const content = document.getElementById('estadoPagoContent');
    
    let estadoClass = 'text-yellow-800 bg-yellow-100';
    let estadoIcon = 'clock';
    let estadoTexto = 'Pendiente';
    
    if (estado === 'approved') {
        estadoClass = 'text-green-800 bg-green-100';
        estadoIcon = 'check-circle';
        estadoTexto = 'Aprobado';
    } else if (estado === 'rejected') {
        estadoClass = 'text-red-800 bg-red-100';
        estadoIcon = 'x-circle';
        estadoTexto = 'Rechazado';
    }
    
    content.innerHTML = `
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full ${estadoClass} mb-4">
                <i data-lucide="${estadoIcon}" class="h-8 w-8"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Pago ${estadoTexto}</h3>
            <p class="text-sm text-gray-600 mb-4">ID de Pago: #${pagoId}</p>
            ${detalles ? `<div class="text-left bg-gray-50 rounded-lg p-3"><pre class="text-xs">${JSON.stringify(detalles, null, 2)}</pre></div>` : ''}
        </div>
    `;
    
    const modal = document.getElementById('modalEstadoPago');
    const modalContent = modal.querySelector('.modal-content');
    
    modal.classList.remove('hidden');
    // Trigger animation
    setTimeout(() => {
        modalContent.style.transform = 'scale(1)';
        modalContent.style.opacity = '1';
    }, 10);
}

window.cerrarModalEstadoPago = function() {
    const modal = document.getElementById('modalEstadoPago');
    const modalContent = modal.querySelector('.modal-content');
    
    modalContent.style.transform = 'scale(0.9)';
    modalContent.style.opacity = '0';
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// ===== FUNCIONES DE APOYO =====
window.abrirModalCambiarPlan = function() {
    window.abrirModalSeleccionarPlan();
}

window.seleccionarPlan = function(planId, nombre, precio) {
    // Abrir modal de selección
    window.abrirModalSeleccionarPlan();
}

// ===== EVENT LISTENERS PRINCIPALES =====
// Los event listeners están en la sección de arriba del archivo
// Esta sección se mantiene para compatibilidad pero los listeners principales 
// están en el DOMContentLoaded de arriba
