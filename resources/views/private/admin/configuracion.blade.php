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
        <!-- Tabs -->
        <nav class="border-b border-gray-200">
            <div class="flex space-x-8 px-6">
            <button
                type="button"
                data-tab="empresa"
                class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-blue-500 py-4 px-1 font-medium text-sm text-blue-600 transition"
                onclick="showTab('empresa')"
            >
                <i data-lucide="briefcase" class="w-4 h-4 mr-1"></i>
                Empresa
            </button>
            <button
                type="button"
                data-tab="password"
                class="tab-button inline-flex items-center whitespace-nowrap border-b-2 border-transparent py-4 px-1 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition"
                onclick="showTab('password')"
            >
                <i data-lucide="lock" class="w-4 h-4 mr-1"></i>
                Contraseña
            </button>
            </div>
        </nav>

        <!-- Tab Contents -->
        <div id="tab-empresa" class="tab-content p-6">
            <form method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Datos de la Empresa</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="nombre" class="block mb-1 text-sm font-medium text-gray-700">Nombre</label>
                        <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        value="{{ old('nombre', $empresa->nombre ?? '') }}"
                        required
                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label for="ruc" class="block mb-1 text-sm font-medium text-gray-700">RUC</label>
                        <input
                        type="text"
                        name="ruc"
                        id="ruc"
                        value="{{ old('ruc', $empresa->ruc ?? '') }}"
                        required
                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email', $empresa->usuario->correo ?? '') }}"
                            required
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label for="telefono" class="block mb-1 text-sm font-medium text-gray-700">Teléfono</label>
                        <input
                            type="text"
                            name="telefono"
                            id="telefono"
                            value="{{ old('telefono', $empresa->telefono ?? '') }}"
                            required
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>
                </div>
                <div>
                    <label for="logo" class="block mb-1 text-sm font-medium text-gray-700">Logo</label>
                    <div class="mt-1 flex items-center space-x-4">
                        <div class="w-20 h-20 rounded-full overflow-hidden border-4 border-gray-300 bg-gray-100 flex items-center justify-center relative">
                        @if($empresa->logo)
                            <img
                            src="{{ asset('storage/'.$empresa->logo) }}"
                            alt="Logo"
                            class="w-20 h-20 rounded-lg object-cover"
                            />
                        @else
                            <i data-lucide="image" class="w-8 h-8 text-gray-400"></i>
                        @endif
                        </div>
                        <input
                        type="file"
                        name="logo"
                        accept="image/*"
                        class="text-sm text-gray-500 file:py-1 file:px-3 file:border-0 file:rounded-full file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        />
                    </div>
                </div>
                <div class="flex justify-end">
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Guardar Empresa
                </button>
                </div>
            </div>
            </form>
        </div>

        <div id="tab-password" class="tab-content p-6 hidden">
            <form method="POST">
            @csrf @method('PUT')
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Cambiar Contraseña</h3>
                <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="current_password" class="block mb-1 text-sm font-medium text-gray-700">Contraseña Actual</label>
                    <input
                    type="password"
                    name="current_password"
                    id="current_password"
                    required
                    class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>
                <div>
                    <label for="new_password" class="block mb-1 text-sm font-medium text-gray-700">Nueva Contraseña</label>
                    <input
                    type="password"
                    name="new_password"
                    id="new_password"
                    required
                    class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>
                </div>
                <div>
                <label for="new_password_confirmation" class="block mb-1 text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
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
                    Actualizar Contraseña
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
    function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => {
      el.classList.add('hidden');
    });
    document.querySelectorAll('.tab-button').forEach(btn => {
      btn.classList.remove('border-blue-500', 'text-blue-600');
      btn.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById(`tab-${tab}`).classList.remove('hidden');
    const activeBtn = document.querySelector(`.tab-button[data-tab="${tab}"]`);
    activeBtn.classList.add('border-blue-500', 'text-blue-600');
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
  }
</script>
@endpush
