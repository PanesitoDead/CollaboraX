{{-- resources/views/super-admin/auditoria.blade.php --}}
@extends('layouts.super-admin.super-admin')

@section('title', 'Auditoría del Sistema')
@section('page-title', 'Auditoría del Sistema')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Registro de Auditoría</h1>
            <p class="text-gray-600">Monitorea todas las actividades del sistema</p>
        </div>
        <div class="flex items-center gap-2">
            <button id="limpiarBtn" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i data-lucide="trash" class="w-4 h-4 mr-2"></i>
                Limpiar Antiguos
            </button>
            <button id="estadisticasBtn" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-lucide="bar-chart" class="w-4 h-4 mr-2"></i>
                Estadísticas
            </button>
        </div>
    </div>
    
    {{-- Filters --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300">
        <form class="flex flex-wrap gap-4" method="GET">
            <div class="relative flex-1 min-w-64">
                <i data-lucide="search" class="absolute left-3 top-3 h-4 w-4 text-gray-400"></i>
                <input type="text" name="searchTerm" value="{{ request('searchTerm') }}" 
                       placeholder="Buscar por descripción, modelo o usuario..." 
                       onkeyup="setTimeout(() => {this.form.submit()}, 500)" 
                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            
            <div class="min-w-48 relative">
                <select name="filters[subject_type]" onchange="this.form.submit()"
                    class="w-full pr-4 pl-3 py-2 border border-gray-300 rounded-lg
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        appearance-none">
                    <option value="">Todos los modelos</option>
                    @foreach($modelos as $modelo)
                        <option value="{{ $modelo['value'] }}" {{ request('filters.subject_type') == $modelo['value'] ? 'selected' : '' }}>
                            {{ $modelo['label'] }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                    <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500"></i>
                </div>
            </div>
            
            <div class="min-w-48 relative">
                <select name="filters[event]" onchange="this.form.submit()"
                    class="w-full pr-4 pl-3 py-2 border border-gray-300 rounded-lg
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        appearance-none">
                    <option value="">Todos los eventos</option>
                    @foreach($eventos as $evento)
                        <option value="{{ $evento }}" {{ request('filters.event') == $evento ? 'selected' : '' }}>
                            {{ ucfirst($evento) }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                    <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500"></i>
                </div>
            </div>
            
            <div class="min-w-40">
                <input type="date" name="filters[fecha_desde]" value="{{ request('filters.fecha_desde') }}" 
                       onchange="this.form.submit()"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="Desde">
            </div>
            
            <div class="min-w-40">
                <input type="date" name="filters[fecha_hasta]" value="{{ request('filters.fecha_hasta') }}" 
                       onchange="this.form.submit()"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="Hasta">
            </div>
            
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                Filtrar
            </button>
        </form>
    </div>
    
    {{-- Table --}}
    @include('partials.super-admin.tablas.pag.auditoria-tabla-pag')
</div>

{{-- Modal para estadísticas --}}
<div id="estadisticasModal" class="fixed inset-0 w-screen h-screen bg-black/30 hidden opacity-0 transition-opacity duration-300" style="z-index: 9999;" role="dialog" aria-modal="true">
    <!-- Overlay clickeable para cerrar -->
    <div class="absolute inset-0" onclick="cerrarEstadisticas()"></div>
    
    <!-- Modal Content -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg transform scale-95 transition-transform duration-300 overflow-hidden">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Estadísticas de Auditoría</h3>
                <div id="estadisticasContent" class="mt-4">
                    <!-- Contenido cargado dinámicamente -->
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                <button type="button" onclick="cerrarEstadisticas()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal para limpiar registros --}}
<div id="limpiarModal" class="fixed inset-0 w-screen h-screen bg-black/30 hidden opacity-0 transition-opacity duration-300" style="z-index: 9999;" role="dialog" aria-modal="true">
    <!-- Overlay clickeable para cerrar -->
    <div class="absolute inset-0" onclick="cerrarLimpiar()"></div>
    
    <!-- Modal Content -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg transform scale-95 transition-transform duration-300 overflow-hidden">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i data-lucide="alert-triangle" class="h-6 w-6 text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Limpiar Registros Antiguos</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                ¿Cuántos días de registros deseas mantener? Los registros más antiguos serán eliminados permanentemente.
                            </p>
                            <div class="mt-4">
                                <label for="diasMantener" class="block text-sm font-medium text-gray-700">Días a mantener:</label>
                                <input type="number" id="diasMantener" min="1" max="365" value="90" 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                <button type="button" onclick="confirmarLimpieza()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Limpiar
                </button>
                <button type="button" onclick="cerrarLimpiar()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Funciones para estadísticas
document.getElementById('estadisticasBtn').addEventListener('click', function() {
    mostrarEstadisticas();
});

function mostrarEstadisticas() {
    fetch('{{ route("super-admin.auditoria.estadisticas") }}')
        .then(response => response.json())
        .then(data => {
            let content = `
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-800">Total Registros</h4>
                        <p class="text-2xl font-bold text-blue-600">${data.estadisticas.total_registros}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-green-800">Hoy</h4>
                        <p class="text-2xl font-bold text-green-600">${data.estadisticas.registros_hoy}</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-yellow-800">Esta Semana</h4>
                        <p class="text-2xl font-bold text-yellow-600">${data.estadisticas.registros_semana}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-purple-800">Este Mes</h4>
                        <p class="text-2xl font-bold text-purple-600">${data.estadisticas.registros_mes}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <h4 class="font-semibold mb-2">Top Modelos</h4>
                    <div class="space-y-2">`;
            
            data.por_modelo.forEach(modelo => {
                content += `
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                        <span>${modelo.subject_type.split('\\\\').pop()}</span>
                        <span class="font-bold">${modelo.total}</span>
                    </div>`;
            });
            
            content += `</div></div>`;
            
            document.getElementById('estadisticasContent').innerHTML = content;
            
            // Mostrar modal con animación
            const modal = document.getElementById('estadisticasModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.transform').classList.remove('scale-95');
            }, 10);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar estadísticas');
        });
}

function cerrarEstadisticas() {
    const modal = document.getElementById('estadisticasModal');
    modal.classList.add('opacity-0');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Funciones para limpiar registros
document.getElementById('limpiarBtn').addEventListener('click', function() {
    const modal = document.getElementById('limpiarModal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10);
});

function confirmarLimpieza() {
    const dias = document.getElementById('diasMantener').value;
    
    if (!dias || dias < 1) {
        alert('Por favor ingresa un número válido de días');
        return;
    }
    
    fetch('{{ route("super-admin.auditoria.limpiar") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ dias: parseInt(dias) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error al limpiar registros');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al limpiar registros');
    });
}

function cerrarLimpiar() {
    const modal = document.getElementById('limpiarModal');
    modal.classList.add('opacity-0');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Cerrar modales al hacer clic en el overlay
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modales con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const estadisticasModal = document.getElementById('estadisticasModal');
            const limpiarModal = document.getElementById('limpiarModal');
            
            if (!estadisticasModal.classList.contains('hidden')) {
                cerrarEstadisticas();
            }
            if (!limpiarModal.classList.contains('hidden')) {
                cerrarLimpiar();
            }
        }
    });
});
</script>
@endpush
