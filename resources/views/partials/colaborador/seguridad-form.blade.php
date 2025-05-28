<form 
    id="password-form" 
    action="{{ route('colaborador.configuracion') }}" 
    method="POST" 
    class="space-y-6"
>
    @csrf
    @method('PUT')

    <h3 class="text-lg font-medium text-gray-900">Cambiar Contraseña</h3>

    <div>
        <label for="current_password" class="block mb-1 text-sm font-medium text-gray-700">
            Contraseña actual
        </label>
        <input 
            type="password"
            name="current_password"
            id="current_password"
            required
            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
    </div>

    <div>
        <label for="new_password" class="block mb-1 text-sm font-medium text-gray-700">
            Nueva contraseña
        </label>
        <input 
            type="password"
            name="new_password"
            id="new_password"
            required
            minlength="8"
            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
        <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres</p>
    </div>

    <div>
        <label for="new_password_confirmation" class="block mb-1 text-sm font-medium text-gray-700">
            Confirmar nueva contraseña
        </label>
        <input 
            type="password"
            name="new_password_confirmation"
            id="new_password_confirmation"
            required
            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
    </div>

    <div class="flex justify-end">
        <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
            Actualizar contraseña
        </button>
    </div>
</form>
