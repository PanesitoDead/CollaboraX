<div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-50 border-b border-gray-300">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Nombre
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Rol
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Última conexión
          </th>
          <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
            Acciones
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        @forelse($miembros as $miembro)
          <tr class="hover:bg-gray-50">
            {{-- Nombre --}}
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <img
                  class="h-10 w-10 rounded-full object-cover"
                  src="{{ asset($miembro['avatar']) }}"
                  alt="{{ $miembro['nombre'] }}"
                >
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ $miembro['nombre'] }}</div>
                  <div class="text-sm text-gray-500">{{ $miembro['email'] }}</div>
                </div>
              </div>
            </td>

            {{-- Rol --}}
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                  {{ $miembro['rol'] === 'Coordinador'
                     ? 'bg-blue-100 text-blue-800'
                     : 'bg-green-100 text-green-800' }}"
              >
                {{ $miembro['rol'] }}
              </span>
            </td>

            {{-- Última conexión --}}
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
              <i data-lucide="clock" class="w-4 h-4 inline-block mr-1 text-gray-400"></i>
              {{ \Carbon\Carbon::parse($miembro['last_seen'])->diffForHumans() }}
            </td>

            {{-- Acciones --}}
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end gap-2">
                {{-- <button
                  onclick="viewProfile('{{ $miembro['id'] }}')"
                  class="inline-flex items-center px-3 py-2 text-blue-600 hover:text-blue-900 transition-colors"
                >
                  <i data-lucide="user" class="w-4 h-4 mr-1"></i>
                  Perfil
                </button> --}}
                <button
                  onclick="sendMessage('{{ $miembro['id'] }}')"
                  class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                  <i data-lucide="message-circle" class="w-4 h-4 mr-1"></i>
                  Mensaje
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
              <i data-lucide="users" class="mx-auto h-12 w-12 text-gray-400"></i>
              <h3 class="mt-2 text-sm font-medium">No hay miembros en el equipo.</h3>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
