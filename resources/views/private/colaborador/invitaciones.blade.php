@extends('layouts.private.colaborador')

@section('title', 'Invitaciones')

@section('content')
<div class="space-y-6 p-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Invitaciones</h1>
            <p class="text-gray-600">Gestiona tus invitaciones a equipos y proyectos</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-lg border border-gray-300 overflow-hidden">
        <nav class="border-b border-gray-200">
            <div class="flex space-x-8 px-6">
                @php
                    $current = request()->segment(3);
                    if (!$current) {
                        $current = 'pendiente'; // Default to 'pendiente' if no segment is present
                    }
                @endphp

                <a href="{{ url('colaborador/invitaciones/pendiente') }}"
                   class="inline-flex items-center whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition
                      {{ $current === 'pendiente' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                    Pendientes
                    @if($nro_invitacionesPendientes > 0)
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $nro_invitacionesPendientes }}
                        </span>
                    @endif
                </a>

                <a href="{{ url('colaborador/invitaciones/historial') }}"
                   class="inline-flex items-center whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition
                      {{ $current === 'historial' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i data-lucide="history" class="w-4 h-4 mr-1"></i>
                    Historial
                </a>
            </div>
        </nav>

        <div class="p-6">
             @include('partials.colaborador.tablas.pag.invitaciones-tabla-pag')
        </div>
    </div>

</div>
@endsection
