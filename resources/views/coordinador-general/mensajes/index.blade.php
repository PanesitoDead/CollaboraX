@extends('layouts.coordinador-general.app')

@section('content')
<div class="flex flex-col h-[calc(100vh-4rem)]">
    <!-- Header -->
    <div class="p-6 pb-4">
        <h1 class="text-2xl font-bold text-gray-900">Mensajes</h1>
    </div>

    <!-- Main Content -->
    <div class="flex flex-1 overflow-hidden px-6 pb-6 space-x-4">
        <!-- Conversations Panel (1/3) - Recuadro redondeado -->
        <div class="w-1/3 bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col overflow-hidden">
            <!-- Conversations Header -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-medium text-gray-900">Conversaciones</h2>
                    <button onclick="openNewChatModal()" class="w-8 h-8 bg-gray-600 text-white rounded-full flex items-center justify-center hover:bg-gray-700 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <!-- Search -->
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4"></i>
                    <input 
                        type="text" 
                        id="search-input" 
                        placeholder="Buscar conversaciones..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    >
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="flex border-b border-gray-200">
                <button 
                    class="tab-button flex-1 py-3 text-sm font-medium text-center border-b-2 border-blue-500 text-blue-600 tab-transition" 
                    data-tab="all"
                >
                    Todos
                </button>
                <button 
                    class="tab-button flex-1 py-3 text-sm font-medium text-center text-gray-500 hover:text-gray-700 tab-transition" 
                    data-tab="unread"
                >
                    No leídos
                    <span class="ml-1 bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ $stats['unread'] }}</span>
                </button>
            </div>
            
            <!-- Contacts List -->
            <div class="flex-1 overflow-y-auto" id="contacts-container">
                @foreach($contacts as $contact)
                <div 
                    class="contact-item p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors hover-scale {{ $loop->first ? 'bg-blue-50' : '' }}" 
                    data-contact-id="{{ $contact['id'] }}"
                    data-unread="{{ $contact['unreadCount'] > 0 ? 'true' : 'false' }}"
                    data-important="{{ $contact['important'] ? 'true' : 'false' }}"
                >
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <img src="{{ $contact['avatar'] }}" alt="{{ $contact['name'] }}" class="w-10 h-10 rounded-full">
                            @if($contact['online'])
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="contact-name text-sm font-medium text-gray-900 truncate">{{ $contact['name'] }}</p>
                                <p class="contact-time text-xs text-gray-500">{{ $contact['time'] }}</p>
                            </div>
                            <p class="contact-last-message text-sm text-gray-500 truncate">{{ $contact['lastMessage'] }}</p>
                        </div>
                        @if($contact['unreadCount'] > 0)
                        <div class="bg-blue-500 text-white text-xs font-medium px-2 py-1 rounded-full min-w-[20px] text-center">
                            {{ $contact['unreadCount'] }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Chat Panel (2/3) - Recuadro redondeado -->
        <div class="w-2/3 bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col overflow-hidden">
            <!-- Empty State -->
            <div id="empty-state" class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Selecciona un chat</h3>
                    <p class="text-gray-500">Elige un contacto para comenzar a chatear</p>
                </div>
            </div>
            
            <!-- Chat Interface -->
            <div id="chat-interface" class="flex-1 flex flex-col hidden h-full">
                <!-- Chat Header -->
                <div class="border-b border-gray-200 p-4 flex-shrink-0">
                    <div class="flex items-center space-x-3">
                        <img id="chat-avatar" src="/placeholder.svg" alt="" class="w-10 h-10 rounded-full">
                        <div>
                            <h3 id="chat-name" class="font-medium text-gray-900"></h3>
                            <p id="chat-status" class="text-sm text-green-500"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Messages Area - Con altura fija y scroll -->
                <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4" style="height: calc(100vh - 280px); min-height: 300px;">
                    <!-- Messages will be loaded here -->
                </div>
                
                <!-- Message Input Area - Fijo en la parte inferior -->
                <div class="border-t border-gray-200 flex-shrink-0">
                    <!-- File Preview Area - Ahora está justo encima del input -->
                    <div id="file-preview-area" class="hidden px-4 py-2 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i data-lucide="paperclip" class="w-4 h-4 text-gray-500"></i>
                                <span class="text-sm text-gray-600">Archivos adjuntos:</span>
                            </div>
                            <button onclick="clearFiles()" class="text-xs text-red-500 hover:text-red-700">
                                Limpiar todo
                            </button>
                        </div>
                        <div id="file-preview-list" class="mt-2 flex flex-wrap gap-2 max-h-20 overflow-y-auto">
                            <!-- File previews will be added here -->
                        </div>
                    </div>
                    
                    <!-- Message Form -->
                    <form id="message-form" class="flex items-center space-x-2 p-4">
                        @csrf
                        <input type="hidden" id="contact-id" name="contact_id">
                        <input type="file" id="file-input" multiple class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                        <input type="file" id="image-input" multiple class="hidden" accept="image/*">
                        
                        <button type="button" onclick="document.getElementById('file-input').click()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-full hover:bg-gray-100">
                            <i data-lucide="paperclip" class="w-5 h-5"></i>
                        </button>
                        
                        <button type="button" onclick="document.getElementById('image-input').click()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-full hover:bg-gray-100">
                            <i data-lucide="image" class="w-5 h-5"></i>
                        </button>
                        
                        <input 
                            type="text" 
                            id="message-input" 
                            name="message" 
                            placeholder="Escribe un mensaje..." 
                            class="flex-1 px-4 py-2 border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        >
                        
                        <button type="submit" class="p-2 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition-colors">
                            <i data-lucide="send" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Chat Modal -->
<div id="new-chat-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96 max-w-md mx-4 slide-in">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Nuevo Chat</h3>
            <button onclick="closeNewChatModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="new-chat-form">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar trabajador</label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="worker-search" 
                        placeholder="Escribe para buscar..." 
                        class="w-full px-3 py-2 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        autocomplete="off"
                    >
                    <div id="worker-results" class="absolute z-10 w-full bg-white border border-gray-200 rounded-md shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                        <!-- Search results will appear here -->
                    </div>
                </div>
                <input type="hidden" id="selected-worker-id" name="contact_id">
                <div id="selected-worker" class="hidden mt-2 p-2 bg-blue-50 rounded-md">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <img id="selected-worker-avatar" src="/placeholder.svg" alt="" class="w-8 h-8 rounded-full">
                            <div>
                                <p id="selected-worker-name" class="text-sm font-medium text-gray-900"></p>
                                <p id="selected-worker-role" class="text-xs text-gray-500"></p>
                            </div>
                        </div>
                        <button type="button" onclick="clearSelectedWorker()" class="text-red-500 hover:text-red-700">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Mensaje</label>
                <textarea 
                    name="message" 
                    rows="3" 
                    placeholder="Escribe tu mensaje..." 
                    class="w-full px-3 py-2 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                ></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button 
                    type="button" 
                    onclick="closeNewChatModal()" 
                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                >
                    Cancelar
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                >
                    Iniciar Chat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Pasar datos iniciales y URLs de rutas a JavaScript
    window.appData = {
        initialContacts: @json($contacts),
        initialStats: @json($stats),
        allWorkers: @json($allWorkers), // Pasa todos los trabajadores para la búsqueda inicial del modal
        routes: {
            searchWorkers: "{{ route('coordinador-general.mensajes.search-workers') }}",
            getMessages: "{{ route('coordinador-general.mensajes.get-messages', ['contactId' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', ''),
            sendMessage: "{{ route('coordinador-general.mensajes.send') }}",
            newChat: "{{ route('coordinador-general.mensajes.new-chat') }}",
            csrfToken: "{{ csrf_token() }}"
        }
    };
</script>
{{-- Carga el archivo JavaScript externo --}}
<script src="{{ asset('js/mensajes.js') }}" defer></script>

<style>
.sidebar-transition {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.tab-transition {
    transition: all 0.2s ease-in-out;
}
.form-transition {
    transition: all 0.3s ease-in-out;
}
.hover-scale {
    transition: transform 0.2s ease-in-out;
}
.hover-scale:hover {
    transform: scale(1.02);
}
.notification-badge {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}
.slide-in {
    animation: slideIn 0.3s ease-out;
}
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.fade-in {
    animation: fadeIn 0.5s ease-in;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Asegurar que el contenedor de mensajes mantenga su altura fija */
#messages-container {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

#messages-container::-webkit-scrollbar {
    width: 6px;
}

#messages-container::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 3px;
}

#messages-container::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

#messages-container::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Asegurar que el chat interface use toda la altura disponible */
#chat-interface {
    max-height: 100%;
    overflow: hidden;
}

/* Transición suave para contactos que se mueven */
.contact-item {
    transition: all 0.3s ease;
}
</style>
@endsection
