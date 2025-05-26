<div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-50 border-b border-gray-300">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Invitación</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Respuesta</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        @forelse($historial as $inv)
        <tr class="hover:bg-gray-50">
          {{-- Equipo --}}
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">{{ $inv['equipo'] }}</div>
            <div class="flex items-center text-xs text-gray-500 mt-1 space-x-1">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 
                         00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <span>{{ $inv['coordinador'] }}</span>
            </div>
          </td>

          {{-- Fecha Invitación --}}
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
            {{ \Carbon\Carbon::parse($inv['fecha_invitacion'])->format('d/m/Y, H:i') }}
          </td>

          {{-- Estado --}}
          <td class="px-6 py-4 whitespace-nowrap">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
              {{ $inv['estado'] === 'aceptada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
              @if($inv['estado'] === 'aceptada')
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7"/>
                </svg>
              @else
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"/>
                </svg>
              @endif
              {{ ucfirst($inv['estado']) }}
            </span>
          </td>

          {{-- Fecha Respuesta --}}
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
            {{ \Carbon\Carbon::parse($inv['fecha_respuesta'])->format('d/m/Y, H:i') }}
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="px-6 py-12 text-center text-gray-500">
            <div class="text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 
                         0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 
                         015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 
                         0a5.002 5.002 0 019.288 0M15 7a3 3 0 
                         11-6 0 3 3 0 016 0zm6 3a2 2 0 
                         11-4 0 2 2 0 014 0zM7 10a2 2 0 
                         11-4 0 2 2 0 014 0z"/>
              </svg>
              <h3 class="mt-2 text-sm font-medium">No hay historial de invitaciones</h3>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
