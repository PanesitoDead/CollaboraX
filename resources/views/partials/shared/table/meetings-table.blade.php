<div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-50 border-b border-gray-300">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reunión</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Fecha y Hora</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Ubicación</th>
          <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        @forelse($meetings as $meeting)
          @php
            $meetingDateTime = \Carbon\Carbon::parse($meeting->fecha . ' ' . $meeting->hora);
            $isPast = $meetingDateTime->isPast();
          @endphp
          <tr class="hover:bg-gray-50">
            {{-- Título y descripción --}}
            <td class="px-6 py-4 whitespace-nowrap max-w-[250px]">
              <div class="text-sm font-medium text-gray-900 truncate">{{ $meeting->asunto }}</div>
              <div class="text-sm text-gray-500 truncate">{{ $meeting->descripcion }}</div>
              <div class="md:hidden text-xs text-gray-500 mt-1">
                <i data-lucide="calendar" class="w-4 h-4 inline-block mr-1 text-gray-400"></i>
                {{ $meetingDateTime->format('d/m/Y H:i') }} ({{ $meeting->duracion }} min)
              </div>
            </td>

            {{-- Fecha y hora --}}
            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
              <div class="flex items-center text-sm text-gray-900">
                <i data-lucide="calendar" class="w-4 h-4 mr-1 text-gray-400"></i>
                {{ $meeting->fecha }}
              </div>
              <div class="flex items-center text-sm text-gray-500">
                <i data-lucide="clock" class="w-4 h-4 mr-1 text-gray-400"></i>
                {{ $meeting->hora }} ({{ $meeting->duracion }} min)
              </div>
            </td>

            {{-- Ubicación / Modalidad --}}
            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
              <div class="flex items-center">
                <i data-lucide="map-pin" class="w-4 h-4 mr-1 text-gray-400"></i>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                  {{ $meeting->modalidad->nombre ?? 'Sin modalidad' }}
                </span>
                <span class="text-sm text-gray-500 truncate max-w-[150px]">{{ $meeting->sala }}</span>
              </div>
            </td>

            {{-- Acciones --}}
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end gap-2">
                @if($showJoin)
                  <a
                    href="{{ $meeting->link_participante }}"
                    target="_blank"
                    class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                  >
                    <i data-lucide="log-in" class="w-4 h-4 mr-1"></i>
                    Unirse
                  </a>
                @endif

                @if(!$isPast && $showEdit)
                  <button
                    onclick="editMeeting('{{ $meeting->id }}')"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors"
                  >
                    <i data-lucide="edit" class="w-4 h-4 mr-1"></i>
                    Editar
                  </button>
                @endif

                @if($showDetails)
                  <button
                    onclick="viewMeetingDetails('{{ $meeting->id }}')"
                    class="inline-flex items-center px-3 py-2 text-indigo-600 hover:text-indigo-900 transition-colors"
                  >
                    <i data-lucide="info" class="w-4 h-4 mr-1"></i>
                    Detalles
                  </button>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
              <i data-lucide="calendar-off" class="mx-auto h-12 w-12 text-gray-400"></i>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No hay reuniones programadas</h3>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Paginación --}}
  @if($meetingsPaginator->hasPages())
    <div class="px-6 py-3 border-t border-gray-200">
      {{ $meetingsPaginator->links() }}
    </div>
  @endif
</div>

{{-- Scripts --}}
@push('scripts')
<script>
  function editMeeting(id) {
    // implementar
  }

  function viewMeetingDetails(id) {
    // implementar
  }
</script>
@endpush
