<div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-50 border-b border-gray-300">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Invitación</th>
          <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        @forelse($invitaciones as $inv)
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

          {{-- Fecha de Invitación --}}
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
            <div class="flex items-center space-x-1">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>{{ \Carbon\Carbon::parse($inv['fecha_invitacion'])->format('d/m/Y, H:i') }}</span>
            </div>
          </td>

          {{-- Acciones --}}
          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <button
              onclick="aceptarInvitacion({{ $inv['id'] }})"
              class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 13l4 4L19 7"/>
              </svg>
              Aceptar
            </button>
            <button
              onclick="rechazarInvitacion({{ $inv['id'] }})"
              class="px-3 py-2 border border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition-colors ml-2"
            >
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Rechazar
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="3" class="px-6 py-12 text-center text-gray-500">
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
              <h3 class="mt-2 text-sm font-medium">No hay invitaciones pendientes</h3>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
