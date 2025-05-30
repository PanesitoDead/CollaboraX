<div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-50 border-b border-gray-300">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Equipo
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Fecha Invitación
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Estado
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Fecha Respuesta
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        @forelse($historial as $inv)
          <tr class="hover:bg-gray-50">
            {{-- Equipo --}}
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">
                {{ $inv['equipo'] }}
              </div>
              <div class="flex items-center text-xs text-gray-500 mt-1 space-x-1">
                <i data-lucide="users" class="w-4 h-4 text-gray-400"></i>
                <span>{{ $inv['coordinador'] }}</span>
              </div>
            </td>

            {{-- Fecha Invitación --}}
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
              <i data-lucide="calendar" class="w-4 h-4 inline-block mr-1 text-gray-400"></i>
              {{ \Carbon\Carbon::parse($inv['fecha_invitacion'])->format('d/m/Y, H:i') }}
            </td>

            {{-- Estado --}}
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                {{ $inv['estado'] === 'aceptada'
                   ? 'bg-green-100 text-green-800'
                   : 'bg-red-100 text-red-800' }}">
                @if($inv['estado'] === 'aceptada')
                  <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                @else
                  <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i>
                @endif
                {{ ucfirst($inv['estado']) }}
              </span>
            </td>

            {{-- Fecha Respuesta --}}
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
              <i data-lucide="clock" class="w-4 h-4 inline-block mr-1 text-gray-400"></i>
              {{ \Carbon\Carbon::parse($inv['fecha_respuesta'])->format('d/m/Y, H:i') }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
              <i data-lucide="inbox" class="mx-auto h-12 w-12 text-gray-400"></i>
              <h3 class="mt-2 text-sm font-medium">No hay historial de invitaciones</h3>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
