{{-- Datos básicos de la empresa --}}
<div class="bg-gray-50 rounded-lg p-4">
    <h4 class="text-md font-medium text-gray-900 mb-4 flex items-center">
        <i data-lucide="building" class="w-4 h-4 mr-2 text-blue-600"></i>
        Información General
    </h4>
    
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="nombre" class="block mb-1 text-sm font-medium text-gray-700">
                Nombre de la Empresa *
            </label>
            <input
                type="text"
                name="nombre"
                id="nombre"
                value="{{ old('nombre', $empresa->nombre ?? '') }}"
                required
                placeholder="Ingrese el nombre de su empresa"
                maxlength="255"
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('nombre') border-red-500 @enderror"
            />
            @error('nombre')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="ruc" class="block mb-1 text-sm font-medium text-gray-700">
                RUC / Número de Identificación *
            </label>
            <input
                type="text"
                name="ruc"
                id="ruc"
                value="{{ old('ruc', $empresa->ruc ?? '') }}"
                required
                placeholder="Ej: 20123456789"
                maxlength="255"
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('ruc') border-red-500 @enderror"
            />
            @error('ruc')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="telefono" class="block mb-1 text-sm font-medium text-gray-700">
                Teléfono *
            </label>
            <input
                type="tel"
                name="telefono"
                id="telefono"
                value="{{ old('telefono', $empresa->telefono ?? '') }}"
                required
                placeholder="Ej: +51 987 654 321"
                maxlength="255"
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('telefono') border-red-500 @enderror"
            />
            @error('telefono')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
    
    <div class="mt-4">
        <label for="descripcion" class="block mb-1 text-sm font-medium text-gray-700">
            Descripción
        </label>
        <textarea
            name="descripcion"
            id="descripcion"
            rows="3"
            placeholder="Descripción breve de su empresa (opcional)"
            maxlength="255"
            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-none @error('descripcion') border-red-500 @enderror"
        >{{ old('descripcion', $empresa->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-500">Máximo 255 caracteres</p>
    </div>
</div>
