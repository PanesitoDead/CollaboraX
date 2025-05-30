<div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-50 border-b border-gray-300">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Equipo
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Fecha de Invitación
          </th>
          <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
            Acciones
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        @forelse($invitaciones as $inv)
          <tr class="hover:bg-gray-50">
            {{-- Equipo --}}
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ $inv['equipo'] }}</div>
              <div class="flex items-center text-xs text-gray-500 mt-1 space-x-1">
                <i data-lucide="users" class="w-4 h-4 text-gray-400"></i>
                <span>{{ $inv['coordinador'] }}</span>
              </div>
            </td>

            {{-- Fecha de Invitación --}}
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
              <div class="flex items-center space-x-1">
                <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                <span>{{ \Carbon\Carbon::parse($inv['fecha_invitacion'])->format('d/m/Y, H:i') }}</span>
              </div>
            </td>

            {{-- Acciones --}}
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="inline-flex items-center justify-end gap-2">
                <button
                  onclick="aceptarInvitacion({{ $inv['id'] }})"
                  class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                  <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                  Aceptar
                </button>
                <button
                  onclick="rechazarInvitacion({{ $inv['id'] }})"
                  class="inline-flex items-center px-3 py-2 border border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                >
                  <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i>
                  Rechazar
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="px-6 py-12 text-center text-gray-500">
              <i data-lucide="inbox" class="mx-auto h-12 w-12 text-gray-400"></i>
              <h3 class="mt-2 text-sm font-medium">No hay invitaciones pendientes</h3>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
