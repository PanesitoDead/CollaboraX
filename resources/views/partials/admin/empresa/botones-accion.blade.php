{{-- Botones de acción del formulario --}}
<div class="bg-white border-t border-gray-200 px-6 py-4 rounded-b-lg">
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-500">
            <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
            Los campos marcados con (*) son obligatorios
        </div>
        
        <div class="flex space-x-3">
            <button
                type="button"
                onclick="resetForm()"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
            >
                <i data-lucide="rotate-ccw" class="w-4 h-4 mr-1 inline"></i>
                Restablecer
            </button>
            
            <button
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                id="submit-btn"
            >
                <span class="submit-text">
                    <i data-lucide="save" class="w-4 h-4 mr-1 inline"></i>
                    Guardar Cambios
                </span>
                <span class="loading-text hidden">
                    <svg class="animate-spin w-4 h-4 mr-1 inline" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Guardando...
                </span>
            </button>
        </div>
    </div>
</div>

<script>
function resetForm() {
    if (confirm('¿Está seguro de que desea restablecer todos los cambios?')) {
        document.getElementById('empresa-form').reset();
        
        // Resetear preview del logo si existe
        const logoPreview = document.getElementById('logo-preview');
        const logoPlaceholder = document.getElementById('logo-placeholder');
        
        @if(!isset($empresa->logo) || !$empresa->logo)
            if (logoPreview) {
                const container = logoPreview.parentElement;
                container.innerHTML = `
                    <div id="logo-placeholder" class="text-center">
                        <i data-lucide="building" class="w-8 h-8 text-gray-400 mx-auto mb-1"></i>
                        <p class="text-xs text-gray-500">Sin logo</p>
                    </div>
                `;
                // Reinicializar los iconos de Lucide
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        @endif
        
        // Resetear campo de eliminar logo
        document.getElementById('remove-logo-input').value = '0';
    }
}

// Agregar loading state al formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('empresa-form');
    const submitBtn = document.getElementById('submit-btn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.querySelector('.submit-text').classList.add('hidden');
            submitBtn.querySelector('.loading-text').classList.remove('hidden');
        });
    }
});
</script>
