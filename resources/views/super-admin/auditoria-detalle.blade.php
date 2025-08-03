{{-- resources/views/super-admin/auditoria-detalle.blade.php --}}
@extends('layouts.super-admin.super-admin')

@section('title', 'Detalle de Auditoría')
@section('page-title', 'Detalle de Auditoría')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detalle de Auditoría #{{ $auditoria->id }}</h1>
            <p class="text-gray-600">Información completa del registro de auditoría</p>
        </div>
        <div>
            <a href="{{ route('super-admin.auditoria.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Volver al Listado
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Información General --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información General</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-500">ID:</span>
                    <span class="text-gray-900">{{ $auditoria->id }}</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-500">Fecha/Hora:</span>
                    <span class="text-gray-900">{{ $auditoria->fecha_formateada }}</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-500">Evento:</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{
                        $auditoria->event == 'created' ? 'bg-green-100 text-green-800' : (
                        $auditoria->event == 'updated' ? 'bg-yellow-100 text-yellow-800' : 
                        'bg-red-100 text-red-800'
                        )
                    }}">
                        {{ ucfirst($auditoria->event) }}
                    </span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-500">Usuario:</span>
                    <div class="text-right">
                        <div class="text-gray-900">{{ $auditoria->usuario_accion }}</div>
                        @if($auditoria->causer_id)
                            <div class="text-xs text-gray-500">ID: {{ $auditoria->causer_id }}</div>
                        @endif
                    </div>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-500">Modelo:</span>
                    <div class="text-right">
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ $auditoria->subject_type }}</code>
                        @if($auditoria->subject_id)
                            <div class="text-xs text-gray-500 mt-1">ID: {{ $auditoria->subject_id }}</div>
                        @endif
                    </div>
                </div>
                
                @if($auditoria->description)
                <div class="py-2">
                    <span class="font-medium text-gray-500 block mb-2">Descripción:</span>
                    <div class="bg-gray-50 p-3 rounded-lg text-gray-900">
                        {{ $auditoria->description }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Estado Actual del Objeto --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Objeto Afectado</h3>
            
            @if($auditoria->subject)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center mb-2">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-2"></i>
                        <span class="font-medium text-green-800">Objeto Existe</span>
                    </div>
                    <p class="text-sm text-green-700">El objeto aún existe en el sistema.</p>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-700 mb-2">Estado Actual:</h4>
                    <pre class="text-xs text-gray-600 overflow-x-auto whitespace-pre-wrap">{{ json_encode($auditoria->subject->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mr-2"></i>
                        <span class="font-medium text-yellow-800">Objeto No Disponible</span>
                    </div>
                    <p class="text-sm text-yellow-700">El objeto ya no existe en el sistema o ha sido eliminado.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Cambios Realizados --}}
    @if($auditoria->properties && count($auditoria->properties) > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cambios Realizados</h3>
            
            @if($auditoria->event === 'updated' && isset($auditoria->properties['old']) && isset($auditoria->properties['attributes']))
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    {{-- Valores Anteriores --}}
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                            <i data-lucide="minus-circle" class="w-4 h-4 text-red-500 mr-2"></i>
                            Valores Anteriores
                        </h4>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <pre class="text-xs text-red-700 overflow-x-auto whitespace-pre-wrap">{{ json_encode($auditoria->properties['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    
                    {{-- Nuevos Valores --}}
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                            <i data-lucide="plus-circle" class="w-4 h-4 text-green-500 mr-2"></i>
                            Nuevos Valores
                        </h4>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <pre class="text-xs text-green-700 overflow-x-auto whitespace-pre-wrap">{{ json_encode($auditoria->properties['attributes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                </div>
                
                {{-- Tabla de Comparación --}}
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Campos Modificados</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-300 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 border-b border-gray-300">Campo</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 border-b border-gray-300">Valor Anterior</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 border-b border-gray-300">Nuevo Valor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($auditoria->properties['old'] as $field => $oldValue)
                                    @if(isset($auditoria->properties['attributes'][$field]))
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $field }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">
                                                    {{ $oldValue ?? 'NULL' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                                    {{ $auditoria->properties['attributes'][$field] ?? 'NULL' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-700 mb-2">Propiedades del Evento:</h4>
                    <pre class="text-xs text-gray-600 overflow-x-auto whitespace-pre-wrap">{{ json_encode($auditoria->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
pre {
    max-height: 300px;
    overflow-y: auto;
}
code {
    background-color: #f3f4f6;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.875rem;
}
</style>
@endpush
