<div
  class="bg-white rounded-lg border border-gray-300 p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
  onclick="openActivityModal('{{ $actividad['id'] }}')"
>
  <div class="flex items-start justify-between mb-3">
    <h4 class="text-sm font-medium text-gray-900 line-clamp-2">
      {{ $actividad['title'] }}
    </h4>
    @php
      $priorityColors = [
        'alta'  => 'bg-red-100 text-red-800',
        'media' => 'bg-yellow-100 text-yellow-800',
        'baja'  => 'bg-green-100 text-green-800',
      ];
    @endphp
    <span
      class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$actividad['priority']] }}"
    >
      {{ ucfirst($actividad['priority']) }}
    </span>
  </div>

  <p class="text-sm text-gray-500 mb-4 line-clamp-2">
    {{ $actividad['description'] }}
  </p>

  <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
    <span>{{ $actividad['team'] }}</span>
    <span>{{ \Carbon\Carbon::parse($actividad['due_date'])->format('d/m/Y') }}</span>
  </div>

  @if($actividad['goal'])
    <div class="mt-2 pt-2 border-t border-gray-200">
      <div class="flex items-center text-sm text-gray-500">
        <i data-lucide="target" class="h-4 w-4 mr-2"></i>
        <span class="truncate">{{ $actividad['goal'] }}</span>
      </div>
    </div>
  @endif

  <div class="mt-4 flex items-center justify-between">
    @if($actividad['assigned_by'])
      <div class="flex items-center text-sm text-gray-500">
        <i data-lucide="user" class="h-4 w-4 mr-2"></i>
        <span>{{ $actividad['assigned_by'] }}</span>
      </div>
    @endif

    <button
      type="button"
      onclick="event.stopPropagation(); openActivityModal('{{ $actividad['id'] }}')"
      class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
      Ver
    </button>
  </div>
</div>
