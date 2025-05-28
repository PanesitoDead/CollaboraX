{{-- resources/views/admin/configuracion.blade.php --}}
@extends('layouts.private.admin')

@section('title', 'Configuración')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Configuración</h1>
            <p class="text-gray-600">Ajustes generales del sistema</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-lg border border-gray-300">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-6 px-6" aria-label="Tabs">
                <button onclick="showTab('empresa')"
                        class="tab-button border-blue-500 text-blue-600 py-3 px-1 border-b-2 font-medium text-sm"
                        data-tab="empresa">
                    Empresa
                </button>
                <button onclick="showTab('usuarios')"
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-3 px-1 border-b-2 font-medium text-sm"
                        data-tab="usuarios">
                    Usuarios
                </button>
            </nav>
        </div>

        {{-- Empresa --}}
        <div id="tab-empresa" class="tab-content p-6">
            <form method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Datos de la Empresa</h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="nombre" id="nombre"
                                   value="{{ old('nombre', $empresa->nombre ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="ruc" class="block text-sm font-medium text-gray-700">RUC</label>
                            <input type="text" name="ruc" id="ruc"
                                   value="{{ old('ruc', $empresa->ruc ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $empresa->email ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700">Logo</label>
                        <div class="mt-1 flex items-center space-x-4">
                            <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                @if($empresa->logo)
                                    <img src="{{ asset('storage/'.$empresa->logo) }}"
                                         alt="Logo" class="h-12 w-12 rounded-lg object-cover">
                                @else
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16"/>
                                    </svg>
                                @endif
                            </div>
                            <input type="file" name="logo" accept="image/*"
                                   class="text-sm text-gray-500 file:py-1 file:px-3 file:border-0 file:rounded-full file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Guardar Empresa
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Usuarios --}}
        <div id="tab-usuarios" class="tab-content p-6 hidden">
            <form method="POST">
                @csrf @method('PUT')
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Ajustes de Usuarios</h3>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-800">Registro abierto</h4>
                            <p class="text-sm text-gray-500">Permitir registro sin invitación</p>
                        </div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="registro_abierto" class="sr-only peer"
                                   {{ old('registro_abierto', $config->registro_abierto ?? false) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer-focus:ring-4 peer-focus:ring-blue-300
                                        peer-checked:bg-blue-600 peer-checked:after:translate-x-full
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                        after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all">
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Guardar Usuarios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showTab(name) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('border-blue-500','text-blue-600');
            btn.classList.add('border-transparent','text-gray-500');
        });
        document.getElementById('tab-'+name).classList.remove('hidden');
        const active = document.querySelector(`.tab-button[data-tab="${name}"]`);
        active.classList.remove('border-transparent','text-gray-500');
        active.classList.add('border-blue-500','text-blue-600');
    }

    document.addEventListener('DOMContentLoaded', () => showTab('empresa'));
</script>
@endpush
