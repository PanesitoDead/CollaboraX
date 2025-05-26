<div class="bg-white rounded-lg border border-gray-300 shadow-sm flex flex-col h-full overflow-hidden">
    {{-- Header --}}
    <div class="px-4 py-3 border-b border-gray-300 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Conversaciones</h3>
        <button
            class="inline-flex items-center justify-center h-10 w-10 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
            title="Nueva conversación"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
        </button>
    </div>

    {{-- Search --}}
    <div class="px-4 py-3 border-b border-gray-200">
        <div class="relative">
            <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                id="search-input"
                type="text"
                placeholder="Buscar conversaciones..."
                class="w-full pl-10 pr-3 h-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-gray-700"
            />
        </div>
    </div>

    {{-- Tabs: Todos y No leídos --}}
    <div class="px-4 py-2 bg-gray-50 border-b border-gray-200">
        <div id="tabs" class="inline-flex w-full bg-white rounded-lg shadow-inner overflow-hidden">
            <button data-tab="all" class="tab-btn flex-1 text-center py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                Todos
            </button>
            <button data-tab="unread" class="tab-btn flex-1 text-center py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                No leídos
            </button>
        </div>
    </div>

    {{-- Contact List --}}
    <div class="flex-1 overflow-auto">
        <div id="contacts-list" class="divide-y divide-gray-200">
            @foreach($contacts as $contact)
                <div
                    class="contact-item flex items-center gap-3 p-3 hover:bg-gray-50 cursor-pointer"
                    data-unread-count="{{ $contact['unreadCount'] ?? 0 }}"
                    onclick="window.location.href='{{ route('colaborador.mensajes', ['contact' => $contact['id']]) }}'"
                >
                    <div class="relative h-10 w-10 rounded-full overflow-hidden">
                        <img src="{{ asset($contact['avatar']) }}" alt="{{ $contact['name'] }}" class="h-full w-full object-cover" />
                        @if($contact['online'])
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-green-500 border-2 border-white"></span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ $contact['name'] }}</span>
                            <span class="text-xs text-gray-500">{{ $contact['time'] ?? '' }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-sm text-gray-500 truncate">{{ $contact['lastMessage'] ?? '' }}</span>
                            @if(!empty($contact['unreadCount']))
                                <span class="inline-flex items-center justify-center h-5 w-5 text-xs font-semibold bg-blue-600 text-white rounded-full">
                                    {{ $contact['unreadCount'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Pestañas: filtro Todos / No leídos
    const tabs = document.querySelectorAll('.tab-btn');
    const contacts = document.querySelectorAll('.contact-item');

    function filterContacts(filter) {
        contacts.forEach(item => {
            const unread = parseInt(item.dataset.unreadCount) > 0;
            const show = filter === 'all' || (filter === 'unread' && unread);
            item.classList.toggle('hidden', !show);
        });
    }

    tabs.forEach(btn => {
        btn.addEventListener('click', () => {
            // estilos tabs
            tabs.forEach(b => {
                b.classList.remove('bg-blue-600', 'text-white');
                b.classList.add('text-gray-700', 'hover:bg-gray-100');
            });
            btn.classList.add('bg-blue-600', 'text-white');
            btn.classList.remove('hover:bg-gray-100');
            // filtrar
            filterContacts(btn.dataset.tab);
        });
    });

    // inicializar en Todos
    filterContacts('all');
    tabs[0].classList.add('bg-blue-600', 'text-white');
    tabs[0].classList.remove('hover:bg-gray-100');
</script>
@endpush
