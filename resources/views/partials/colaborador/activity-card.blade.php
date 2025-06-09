<div
  class="bg-white rounded-lg border border-gray-300 p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
  onclick="abrirModalActividad('{{ $actividad['id'] }}')"
>
  <div class="flex items-start justify-between mb-3">
    <h4 class="text-sm font-medium text-gray-900 line-clamp-2">
      {{ $actividad['nombre'] }}
    </h4>
    {{-- <span
      class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$actividad['priority']] }}"
    >
      {{ ucfirst($actividad['priority']) }}
    </span> --}}
  </div>

  <p class="text-sm text-gray-500 mb-4 line-clamp-2">
    {{ $actividad['descripcion'] }}
  </p>

  <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
    <span>{{ $actividad['equipo'] }}</span>
    <span>{{ \Carbon\Carbon::parse($actividad['fecha_entrega'])->format('d/m/Y') }}</span>
  </div>

  @if($actividad['meta'])
    <div class="mt-2 pt-2 border-t border-gray-200">
      <div class="flex items-center text-sm text-gray-500">
        <i data-lucide="target" class="h-4 w-4 mr-2"></i>
        <span class="truncate">{{ $actividad['meta'] }}</span>
      </div>
    </div>
  @endif

  <div class="mt-4 flex items-center justify-between">
    @if($actividad['asignado_por'])
      <div class="flex items-center text-sm text-gray-500">
        <i data-lucide="user" class="h-4 w-4 mr-2"></i>
        <span>{{ $actividad['asignado_por'] }}</span>
      </div>
    @endif

    <button
      type="button"
      onclick="event.stopPropagation(); abrirModalActividad('{{ $actividad['id'] }}')"
      class="text-blue-600 hover:text-blue-800 focus:outline-none"
      aria-label="Ver detalles de la actividad"
    >
      <i data-lucide="eye" class="h-5 w-5"></i>
    </button>
  </div>
</div>

@include('partials.colaborador.modales.detalles.actividad-modal-detalles')