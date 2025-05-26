<div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
    <div class="px-6 py-5">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Perfil</h3>
        <form id="perfil-form" class="space-y-6">
            @csrf

            {{-- Avatar --}}
            <div class="flex items-center space-x-6">
                <img id="avatar-preview" class="h-16 w-16 rounded-full object-cover border" 
                        src="{{ $profileData['avatar'] }}" alt="Avatar">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Foto de perfil</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG hasta 2 MB</p>
                </div>
            </div>

            {{-- Campos --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach([
                    ['label'=>'Nombre completo','name'=>'name','type'=>'text','value'=>$profileData['name']],
                    ['label'=>'Correo electrónico','name'=>'email','type'=>'email','value'=>$profileData['email']],
                    ['label'=>'Teléfono','name'=>'phone','type'=>'tel','value'=>$profileData['phone']],
                    ['label'=>'Posición','name'=>'position','type'=>'text','value'=>$profileData['position']],
                    ['label'=>'Departamento','name'=>'department','type'=>'text','value'=>$profileData['department']],
                    ['label'=>'Ubicación','name'=>'location','type'=>'text','value'=>$profileData['location']],
                ] as $field)
                <div>
                    <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700">
                        {{ $field['label'] }}
                    </label>
                    <input 
                        type="{{ $field['type'] }}" 
                        name="{{ $field['name'] }}" 
                        id="{{ $field['name'] }}" 
                        value="{{ $field['value'] }}" 
                        @if(in_array($field['name'], ['name','email'])) required @endif
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    >
                </div>
                @endforeach
            </div>

            {{-- Biografía --}}
            <div>
                <label for="bio" class="block text-sm font-medium text-gray-700">Biografía</label>
                <textarea 
                    name="bio" id="bio" rows="3" maxlength="500"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Cuéntanos sobre ti..."
                >{{ $profileData['bio'] }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Máximo 500 caracteres</p>
            </div>

            {{-- Redes Sociales --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach([
                    ['label'=>'LinkedIn','name'=>'linkedin','value'=>$profileData['linkedin'],'placeholder'=>'https://linkedin.com/in/tu-perfil'],
                    ['label'=>'GitHub','name'=>'github','value'=>$profileData['github'],'placeholder'=>'https://github.com/tu-usuario'],
                ] as $social)
                <div>
                    <label for="{{ $social['name'] }}" class="block text-sm font-medium text-gray-700">
                        {{ $social['label'] }}
                    </label>
                    <input 
                        type="url" 
                        name="{{ $social['name'] }}" 
                        id="{{ $social['name'] }}" 
                        value="{{ $social['value'] }}" 
                        placeholder="{{ $social['placeholder'] }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    >
                </div>
                @endforeach
            </div>

            {{-- Submit --}}
            <div class="flex justify-end">
                <button 
                    type="button" 
                    onclick="handleFormSubmit('perfil-form','{{ route('colaborador.configuracion') }}','Perfil actualizado exitosamente')"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>