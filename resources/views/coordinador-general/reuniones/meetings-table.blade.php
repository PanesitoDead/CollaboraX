<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
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
                <tr class="{{ $isPast ? 'opacity-70' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $meeting['title'] }}</div>
                        <div class="text-sm text-gray-500 line-clamp-1">{{ $meeting['description'] }}</div>
                        <div class="md:hidden text-xs text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($meeting['date'])->format('d/m/Y') }} - {{ $meeting['time'] }} ({{ $meeting['duration'] }} min)
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                        <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($meeting['date'])->format('d/m/Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $meeting['time'] }} ({{ $meeting['duration'] }} min)</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-2 
                                {{ $meeting['type'] === 'Virtual' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $meeting['type'] }}
                            </span>
                            <span class="text-sm text-gray-900 truncate max-w-[150px]">{{ $meeting['location'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            @if(!$isPast && $showActions)
                                <button onclick="joinMeeting('{{ $meeting['id'] }}')" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Unirse
                                </button>
                                <button onclick="editMeeting('{{ $meeting['id'] }}')" class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Editar
                                </button>
                            @endif
                            <button onclick="viewMeetingDetails('{{ $meeting['id'] }}')" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Detalles
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        No hay reuniones programadas
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>