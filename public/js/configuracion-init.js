/**
 * Inicialización y eventos para la página de configuración
 */

// Inicializar componentes al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('Configuración cargada');
    
    // Inicializar tab por defecto (suscripción como primera opción)
    initDefaultTab();
    
    // Inicializar switch de renovación automática
    initRenovacionSwitch();
    
    // Configurar listeners para modales
    setupModalListeners();
    
    // Configurar listeners para formularios
    setupFormListeners();
});

function initDefaultTab() {
    // Asegurar que el tab de suscripción esté activo por defecto
    if (typeof window.showTab === 'function') {
        window.showTab('suscripcion');
    }
}

function initRenovacionSwitch() {
    const checkbox = document.getElementById('renovacion-switch');
    if (checkbox) {
        const dot = checkbox.parentElement.querySelector('.dot');
        const bg = checkbox.parentElement.querySelector('.block');
        
        if (checkbox.checked) {
            dot.style.transform = 'translateX(1rem)';
            bg.classList.remove('bg-gray-300');
            bg.classList.add('bg-blue-500');
        } else {
            dot.style.transform = 'translateX(0)';
            bg.classList.remove('bg-blue-500');
            bg.classList.add('bg-gray-300');
        }
    }
}

function setupModalListeners() {
    // Listener para el checkbox de confirmación de cancelación
    const confirmarCheckbox = document.getElementById('confirmarCancelacion');
    const btnConfirmar = document.getElementById('btnConfirmarCancelacion');
    
    if (confirmarCheckbox && btnConfirmar) {
        confirmarCheckbox.addEventListener('change', function() {
            btnConfirmar.disabled = !this.checked;
        });
    }

    // Listener para el checkbox de términos y condiciones
    const aceptarTerminos = document.getElementById('aceptarTerminos');
    if (aceptarTerminos) {
        aceptarTerminos.addEventListener('change', validarFormularioSuscripcion);
    }

    // Listeners para cerrar modales al hacer click fuera
    setupModalCloseListeners();
}

function setupModalCloseListeners() {
    const modalCancelar = document.getElementById('modalCancelarSuscripcion');
    if (modalCancelar) {
        modalCancelar.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModalCancelarSuscripcion();
            }
        });
    }

    const modalSeleccionar = document.getElementById('modalSeleccionarPlan');
    if (modalSeleccionar) {
        modalSeleccionar.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModalSeleccionarPlan(e);
            }
        });
    }

    const modalEstado = document.getElementById('modalEstadoPago');
    if (modalEstado) {
        modalEstado.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModalEstadoPago();
            }
        });
    }
}

function setupFormListeners() {
    // Aquí se pueden agregar más listeners para formularios si es necesario
    console.log('Form listeners configurados');
}
