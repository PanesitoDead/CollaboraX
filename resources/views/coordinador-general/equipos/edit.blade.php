@extends('layouts.coordinador-general.app')

@section('content')
<!-- Header -->
<div class="bg-white border-b border-gray-200 px-6 py-4 slide-in">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('coordinador-general.equipos') }}" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Editar Equipo</h1>
            </div>
            <p class="text-gray-600 mt-1">Modifica la información del equipo</p>
        </div>
    </div>
</div>

<div class="flex-1 overflow-auto p-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 slide-in">
            
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('coordinador-general.equipos.update', $equipo->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre del equipo</label>
                        <input type="text" 
                               id="nombre" 
                               name="nombre" 
                               value="{{ old('nombre', $equipo->nombre) }}" 
                               required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="area_id" class="block text-sm font-medium text-gray-700 mb-2">Área</label>
                        <select id="area_id" 
                                name="area_id" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar área</option>
                            @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ (old('area_id', $equipo->area_id) == $area->id) ? 'selected' : '' }}>
                                {{ $area->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="coordinador_id" class="block text-sm font-medium text-gray-700 mb-2">Coordinador</label>
                        <select id="coordinador_id" 
                                name="coordinador_id" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar coordinador</option>
                            @foreach($coordinadores as $coordinador)
                            <option value="{{ $coordinador['id'] }}" {{ (old('coordinador_id', $equipo->coordinador_id) == $coordinador['id']) ? 'selected' : '' }}>
                                {{ $coordinador['nombre'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea id="descripcion" 
                                  name="descripcion" 
                                  rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('descripcion', $equipo->descripcion) }}</textarea>
                    </div>
                </div>
                
                <div class="flex space-x-3 mt-8">
                    <a href="{{ route('coordinador-general.equipos') }}" 
                       class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors text-center">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Actualizar Equipo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>

<style>
.slide-in {
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection
