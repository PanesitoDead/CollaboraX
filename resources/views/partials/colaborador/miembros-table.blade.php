<div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-300">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actividades</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rendimiento</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($miembros as $miembro)
                    <tr class="hover:bg-gray-50">
                        {{-- Nombre --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset($miembro['avatar']) }}" alt="{{ $miembro['nombre'] }}">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $miembro['nombre'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $miembro['email'] }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Rol --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors {{ $miembro['rol'] === 'Coordinador' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $miembro['rol'] }}
                            </span>
                        </td>

                        {{-- Actividades --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm text-gray-900 mr-2">
                                    {{ $miembro['actividades_completadas'] }}/{{ $miembro['actividades_totales'] }}
                                </span>
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ ($miembro['actividades_totales'] ? ($miembro['actividades_completadas'] / $miembro['actividades_totales']) * 100 : 0) }}%"></div>
                                </div>
                            </div>
                        </td>

                        {{-- Rendimiento --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $miembro['rendimiento'] }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            No hay miembros en el equipo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
