<div class="space-y-4">
    @forelse($metas as $meta)
        <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
            <div class="px-6 py-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ $meta['nombre'] }}</h4>
                        <p class="text-sm text-gray-500">{{ $meta['descripcion'] }}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Vence: {{ $meta['fecha_entrega'] }}
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">Progreso</span>
                        <span class="font-medium text-gray-900">{{ $meta['progreso'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $meta['progreso'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500">
                        {{ $meta['tareas_completadas'] }} de {{ $meta['total_tareas'] }} actividades completadas
                    </p>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-gray-500">
            No hay metas para mostrar.
        </div>
    @endforelse
</div>
