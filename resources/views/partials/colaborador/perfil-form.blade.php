<form id="perfil-form" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf @method('PUT')
    <h3 class="text-lg font-medium text-gray-900">Información del Perfil</h3>

    <!-- Avatar -->
    <div class="flex items-center space-x-4">
        <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center border">
            <img id="avatar-preview" src="{{ $profileData['avatar'] }}" alt="Avatar" class="h-16 w-16 rounded-full object-cover">
        </div>
        <div class="flex-1">
            <label for="avatar" class="block mb-1 text-sm font-medium text-gray-700">Foto de perfil</label>
            <input 
                type="file" 
                id="avatar" 
                name="avatar" 
                accept="image/*"
                class="block w-full text-sm text-gray-500 file:py-1 file:px-3 file:border-0 file:rounded-full file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
            />
            <p class="mt-1 text-xs text-gray-500">PNG, JPG hasta 2 MB</p>
        </div>
    </div>

    <!-- Campos -->
    <div class="grid gap-4 md:grid-cols-2">
        @foreach([
            ['label'=>'Nombre completo','name'=>'name','type'=>'text','value'=>$profileData['name']],
            ['label'=>'Correo electrónico','name'=>'email','type'=>'email','value'=>$profileData['email']],
            ['label'=>'Teléfono','name'=>'phone','type'=>'tel','value'=>$profileData['phone']],
            ['label'=>'Posición','name'=>'position','type'=>'text','value'=>$profileData['position']],
            ['label'=>'Departamento','name'=>'department','type'=>'text','value'=>$profileData['department']],
            ['label'=>'Ubicación','name'=>'location','type'=>'text','value'=>$profileData['location']],
        ] as $field)
        <div>
            <label for="{{ $field['name'] }}" class="block mb-1 text-sm font-medium text-gray-700">{{ $field['label'] }}</label>
            <input
                type="{{ $field['type'] }}"
                name="{{ $field['name'] }}"
                id="{{ $field['name'] }}"
                value="{{ $field['value'] }}"
                @if(in_array($field['name'], ['name','email'])) required @endif
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
        </div>
        @endforeach
    </div>

    <!-- Biografía -->
    <div>
        <label for="bio" class="block mb-1 text-sm font-medium text-gray-700">Biografía</label>
        <textarea
            name="bio"
            id="bio"
            rows="3"
            maxlength="500"
            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Cuéntanos sobre ti..."
        >{{ $profileData['bio'] }}</textarea>
        <p class="mt-1 text-xs text-gray-500">Máximo 500 caracteres</p>
    </div>

    <!-- Redes Sociales -->
    <div class="grid gap-4 md:grid-cols-2">
        @foreach([
            ['label'=>'LinkedIn','name'=>'linkedin','value'=>$profileData['linkedin'],'placeholder'=>'https://linkedin.com/in/tu-perfil'],
            ['label'=>'GitHub','name'=>'github','value'=>$profileData['github'],'placeholder'=>'https://github.com/tu-usuario'],
        ] as $social)
        <div>
            <label for="{{ $social['name'] }}" class="block mb-1 text-sm font-medium text-gray-700">{{ $social['label'] }}</label>
            <input
                type="url"
                name="{{ $social['name'] }}"
                id="{{ $social['name'] }}"
                value="{{ $social['value'] }}"
                placeholder="{{ $social['placeholder'] }}"
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
        </div>
        @endforeach
    </div>

    <!-- Submit -->
    <div class="flex justify-end">
        <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
            Guardar cambios
        </button>
    </div>
</form>