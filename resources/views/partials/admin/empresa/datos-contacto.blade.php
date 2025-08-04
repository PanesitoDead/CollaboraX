{{-- Información de contacto --}}
<div class="bg-gray-50 rounded-lg p-4">
    <h4 class="text-md font-medium text-gray-900 mb-4 flex items-center">
        <i data-lucide="mail" class="w-4 h-4 mr-2 text-green-600"></i>
        Información de Contacto
    </h4>
    
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="correo" class="block mb-1 text-sm font-medium text-gray-700">
                Email Corporativo *
            </label>
            <input
                type="email"
                name="correo"
                id="correo"
                value="{{ old('correo', $empresa->usuario->correo ?? '') }}"
                required
                placeholder="empresa@dominio.com"
                maxlength="255"
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('correo') border-red-500 @enderror"
            />
            @error('correo')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">
                Este será el email principal para notificaciones y acceso al sistema
            </p>
        </div>
        
        <div>
            <label for="correo_personal" class="block mb-1 text-sm font-medium text-gray-700">
                Email Personal
            </label>
            <input
                type="email"
                name="correo_personal"
                id="correo_personal"
                value="{{ old('correo_personal', $empresa->usuario->correo_personal ?? '') }}"
                placeholder="email.personal@dominio.com"
                maxlength="255"
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('correo_personal') border-red-500 @enderror"
            />
            @error('correo_personal')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">
                Email alternativo para comunicaciones importantes (opcional)
            </p>
        </div>
    </div>
</div>
