@extends('layouts.private.admin')

@section('title', 'Áreas - Admin')
@section('page-title', 'Gestión de Áreas')
@section('page-description', 'Administra las áreas de trabajo de la empresa')

@section('content')
<div class="p-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input type="text" placeholder="Buscar áreas..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Crear Área</span>
        </button>
    </div>

    <!-- Areas Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
        $areas = [
            ['name' => 'Desarrollo', 'description' => 'Equipo de desarrollo de software', 'members' => 12, 'projects' => 8, 'color' => 'blue'],
            ['name' => 'Diseño', 'description' => 'Equipo de diseño UX/UI', 'members' => 8, 'projects' => 5, 'color' => 'purple'],
            ['name' => 'Marketing', 'description' => 'Equipo de marketing digital', 'members' => 6, 'projects' => 12, 'color' => 'green'],
            ['name' => 'Ventas', 'description' => 'Equipo comercial y ventas', 'members' => 10, 'projects' => 15, 'color' => 'yellow'],
            ['name' => 'Operaciones', 'description' => 'Gestión operativa', 'members' => 5, 'projects' => 3, 'color' => 'red'],
            ['name' => 'Recursos Humanos', 'description' => 'Gestión de talento humano', 'members' => 4, 'projects' => 2, 'color' => 'indigo']
        ];
        @endphp
        
        @foreach($areas as $area)
        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-{{ $area['color'] }}-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-{{ $area['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $area['name'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $area['description'] }}</p>
                        </div>
                    </div>
                    <div class="relative">
                        <button class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $area['members'] }}</p>
                        <p class="text-sm text-gray-500">Miembros</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $area['projects'] }}</p>
                        <p class="text-sm text-gray-500">Proyectos</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex -space-x-2">
                        @for($i = 0; $i < min(4, $area['members']); $i++)
                        <img class="w-8 h-8 rounded-full border-2 border-white" src="/placeholder-32px.png" alt="">
                        @endfor
                        @if($area['members'] > 4)
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center">
                            <span class="text-xs font-medium text-gray-600">+{{ $area['members'] - 4 }}</span>
                        </div>
                        @endif
                    </div>
                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver detalles</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection