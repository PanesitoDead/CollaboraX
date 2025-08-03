/**
 * Manejo de pestañas en la página de configuración
 */

window.showTab = function(tab) {
    // Ocultar todas las pestañas
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Resetear estilos de todos los botones
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Mostrar la pestaña seleccionada
    document.getElementById(`tab-${tab}`).classList.remove('hidden');
    
    // Activar el botón correspondiente
    const activeBtn = document.querySelector(`.tab-button[data-tab="${tab}"]`);
    activeBtn.classList.add('border-blue-500', 'text-blue-600');
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
}

// Exportar la función si se usa como módulo
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { showTab };
}
