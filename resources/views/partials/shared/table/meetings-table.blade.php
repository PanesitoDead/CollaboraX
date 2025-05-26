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
                        $meetingDateTime = \Carbon\Carbon::parse($meeting['date'] . ' ' . $meeting['time']);
                        $isPast = $meetingDateTime->isPast();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        {{-- Título y descripción --}}
                        <td class="px-6 py-4 whitespace-nowrap max-w-[250px]">
                            <div class="text-sm font-medium text-gray-900 truncate">{{ $meeting['title'] }}</div>
                            <div class="text-sm text-gray-500 truncate">{{ $meeting['description'] }}</div>
                            <div class="md:hidden text-xs text-gray-500 mt-1">
                                {{ $meeting['date'] }} - {{ $meeting['time'] }} ({{ $meeting['duration'] }} min)
                            </div>
                        </td>

                        {{-- Fecha y hora --}}
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            <div class="text-sm text-gray-900">{{ $meeting['date'] }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $meeting['time'] }} ({{ $meeting['duration'] }} min)
                            </div>
                        </td>

                        {{-- Ubicación --}}
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                    {{ $meeting['type'] }}
                                </span>
                                <span class="text-sm text-gray-500 truncate max-w-[150px]">{{ $meeting['location'] }}</span>
                            </div>
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                @if(!$isPast && $showJoin)
                                    <button 
                                        onclick="joinMeeting('{{ $meeting['id'] }}')"
                                        class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                    >
                                        Unirse
                                    </button>
                                @endif

                                @if(!$isPast && $showEdit)
                                    <button 
                                        onclick="editMeeting('{{ $meeting['id'] }}')"
                                        class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors"
                                    >
                                        Editar
                                    </button>
                                @endif

                                @if($showDetails)
                                    <button 
                                        onclick="viewMeetingDetails('{{ $meeting['id'] }}')"
                                        class="px-3 py-2 text-indigo-600 hover:text-indigo-900 transition-colors"
                                    >
                                        Detalles
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <!-- Icono Sin datos -->
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                                                M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                                                m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0
                                                zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay reuniones programadas</h3>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($meetingsPaginator->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $meetingsPaginator->links() }}
        </div>
    @endif
</div>
{{-- Scripts --}}
@push('scripts')
<script>
function joinMeeting(meetingId) {
    // Lógica real: fetch al endpoint de unirse
    console.log(`POST /meetings/${meetingId}/join`);
}

function editMeeting(meetingId) {
    // Redirigir a la ruta de edición
    window.location.href = `/meetings/${meetingId}/edit`;
}

function viewMeetingDetails(meetingId) {
    // Abrir modal o ir a detalles
    window.location.href = `/meetings/${meetingId}`;
}
</script>
@endpush