<div class="bg-white rounded-lg shadow-sm border h-full flex flex-col">
    <div class="p-4 border-b">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Mensajes</h2>
            <button onclick="openNewChatModal()" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i data-lucide="plus" class="h-4 w-4 mr-1"></i>
                Nuevo
            </button>
        </div>
        <div class="mt-3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                </div>
                <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Buscar contactos...">
            </div>
        </div>
    </div>
    
    <div class="flex-1 overflow-y-auto">
        @foreach($contacts as $contact)
            <div class="contact-item p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors duration-150" 
                 data-contact-id="{{ $contact['id'] }}" 
                 onclick="selectContact('{{ $contact['id'] }}')">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <img class="h-10 w-10 rounded-full" src="{{ $contact['avatar'] }}" alt="{{ $contact['name'] }}">
                        @if($contact['online'])
                            <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-green-400 ring-2 ring-white"></span>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $contact['name'] }}</p>
                                @if($contact['group'])
                                    <i data-lucide="users" class="h-3 w-3 text-gray-400"></i>
                                @endif
                                @if($contact['important'])
                                    <i data-lucide="star" class="h-3 w-3 text-yellow-400 fill-current"></i>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <p class="text-xs text-gray-500">{{ $contact['time'] }}</p>
                                @if($contact['unreadCount'] > 0)
                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                        {{ $contact['unreadCount'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 truncate mt-1">{{ $contact['lastMessage'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>