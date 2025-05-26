<div class="space-y-6">
    {{-- Cambiar Contraseña --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
        <div class="px-6 py-5">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cambiar Contraseña</h3>
            <form id="password-form" class="space-y-6">
                @csrf

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Contraseña actual</label>
                    <input 
                        type="password" 
                        name="current_password" 
                        id="current_password" 
                        required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    >
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">Nueva contraseña</label>
                    <input 
                        type="password" 
                        name="new_password" 
                        id="new_password" 
                        required 
                        minlength="8"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    >
                    <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres</p>
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar nueva contraseña</label>
                    <input 
                        type="password" 
                        name="new_password_confirmation" 
                        id="new_password_confirmation" 
                        required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    >
                </div>

                <div class="flex justify-end">
                    <button 
                        type="button" 
                        onclick="handleFormSubmit('password-form','{{ route('colaborador.configuracion') }}','Contraseña actualizada correctamente')"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <i data-lucide="key" class="w-4 h-4 inline mr-2"></i>
                        Actualizar contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Reusa handleFormSubmit definido en configuración
    function showDeleteAccountModal() {
        document.getElementById('delete-account-modal').classList.remove('hidden');
    }
    function hideDeleteAccountModal() {
        document.getElementById('delete-account-modal').classList.add('hidden');
        document.getElementById('delete-account-form').reset();
    }
</script>
@endpush
