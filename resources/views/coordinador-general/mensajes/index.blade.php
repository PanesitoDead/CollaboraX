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
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $contact['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $contact['time'] }}</p>
                            </div>
                            <p class="text-sm text-gray-500 truncate">{{ $contact['lastMessage'] }}</p>
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
                        <input type="file" id="file-input" multiple class="hidden" accept="*/*">
                        
                        <button type="button" onclick="document.getElementById('file-input').click()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-full hover:bg-gray-100">
                            <i data-lucide="paperclip" class="w-5 h-5"></i>
                        </button>
                        
                        <button type="button" onclick="document.getElementById('file-input').click()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-full hover:bg-gray-100">
                            <i data-lucide="image" class="w-5 h-5"></i>
                        </button>
                        
                        <input 
                            type="text" 
                            id="message-input" 
                            name="message" 
                            placeholder="Escribe un mensaje..." 
                            class="flex-1 px-4 py-2 border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        >
                        
                        <button type="button" class="p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-full hover:bg-gray-100">
                            <i data-lucide="smile" class="w-5 h-5"></i>
                        </button>
                        
                        <button type="button" class="p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-full hover:bg-gray-100">
                            <i data-lucide="mic" class="w-5 h-5"></i>
                        </button>
                        
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar contacto</label>
                <select name="contact_id" class="w-full px-3 py-2 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">Selecciona un contacto...</option>
                    @foreach($allContacts as $contact)
                    <option value="{{ $contact['id'] }}">{{ $contact['name'] }} - {{ $contact['role'] }}</option>
                    @endforeach
                </select>
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
// Variables globales
let currentContactId = null;
let activeTab = 'all';
let selectedFiles = [];

// Elementos DOM
const searchInput = document.getElementById('search-input');
const contactsContainer = document.getElementById('contacts-container');
const tabButtons = document.querySelectorAll('.tab-button');
const emptyState = document.getElementById('empty-state');
const chatInterface = document.getElementById('chat-interface');
const messagesContainer = document.getElementById('messages-container');
const messageForm = document.getElementById('message-form');
const messageInput = document.getElementById('message-input');
const contactIdInput = document.getElementById('contact-id');
const chatAvatar = document.getElementById('chat-avatar');
const chatName = document.getElementById('chat-name');
const chatStatus = document.getElementById('chat-status');
const newChatModal = document.getElementById('new-chat-modal');
const newChatForm = document.getElementById('new-chat-form');
const fileInput = document.getElementById('file-input');
const filePreviewArea = document.getElementById('file-preview-area');
const filePreviewList = document.getElementById('file-preview-list');

// Datos de mensajes
const messages = @json($messages);

// Funciones de archivos
function handleFileSelect(event) {
    const files = Array.from(event.target.files);
    files.forEach(file => {
        if (!selectedFiles.find(f => f.name === file.name && f.size === file.size)) {
            selectedFiles.push(file);
        }
    });
    updateFilePreview();
}

function updateFilePreview() {
    if (selectedFiles.length === 0) {
        filePreviewArea.classList.add('hidden');
        return;
    }
    
    filePreviewArea.classList.remove('hidden');
    filePreviewList.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'flex items-center justify-between bg-white p-2 rounded border text-xs';
        
        const fileSize = formatFileSize(file.size);
        const fileIcon = getFileIcon(file.type);
        
        fileItem.innerHTML = `
            <div class="flex items-center space-x-2 flex-1 min-w-0">
                <i data-lucide="${fileIcon}" class="w-3 h-3 text-gray-500 flex-shrink-0"></i>
                <span class="truncate">${file.name}</span>
                <span class="text-gray-400">(${fileSize})</span>
            </div>
            <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700 ml-2">
                <i data-lucide="x" class="w-3 h-3"></i>
            </button>
        `;
        
        filePreviewList.appendChild(fileItem);
    });
    
    lucide.createIcons();
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updateFilePreview();
}

function clearFiles() {
    selectedFiles = [];
    fileInput.value = '';
    updateFilePreview();
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function getFileIcon(mimeType) {
    if (mimeType.startsWith('image/')) return 'image';
    if (mimeType.startsWith('video/')) return 'video';
    if (mimeType.startsWith('audio/')) return 'music';
    if (mimeType.includes('pdf')) return 'file-text';
    if (mimeType.includes('word') || mimeType.includes('document')) return 'file-text';
    if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'file-spreadsheet';
    if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'presentation';
    return 'file';
}

// Seleccionar contacto
function selectContact(contactId) {
    // Actualizar ID actual
    currentContactId = contactId;
    contactIdInput.value = contactId;
    
    // Obtener datos del contacto
    const contactItems = document.querySelectorAll('.contact-item');
    let selectedContact = null;
    
    contactItems.forEach(item => {
        const itemId = parseInt(item.dataset.contactId);
        if (itemId === contactId) {
            // Marcar como seleccionado
            item.classList.add('bg-blue-50');
            
            // Obtener datos del contacto
            const nameElement = item.querySelector('.text-gray-900');
            const onlineIndicator = item.querySelector('.bg-green-500');
            const avatarElement = item.querySelector('img');
            
            if (nameElement && avatarElement) {
                selectedContact = {
                    id: itemId,
                    name: nameElement.textContent.trim(),
                    online: onlineIndicator !== null,
                    avatar: avatarElement.src
                };
            }
            
            // Eliminar badge de no leídos
            const badge = item.querySelector('.bg-blue-500');
            if (badge) {
                badge.remove();
            }
        } else {
            // Desmarcar otros
            item.classList.remove('bg-blue-50');
        }
    });
    
    if (!selectedContact) {
        console.error('No se pudo encontrar el contacto con ID:', contactId);
        return;
    }
    
    // Actualizar interfaz de chat
    emptyState.classList.add('hidden');
    chatInterface.classList.remove('hidden');
    
    // Actualizar header del chat
    chatName.textContent = selectedContact.name;
    chatStatus.textContent = selectedContact.online ? 'En línea' : 'Desconectado';
    chatStatus.className = selectedContact.online ? 'text-sm text-green-500' : 'text-sm text-gray-500';
    chatAvatar.src = selectedContact.avatar;
    
    // Cargar mensajes
    loadMessages(contactId);
    
    // Asegurarse de que el área de previsualización esté oculta al cambiar de contacto
    clearFiles();
}

// Cargar mensajes
function loadMessages(contactId) {
    const contactMessages = messages[contactId] || [];
    messagesContainer.innerHTML = '';
    
    contactMessages.forEach(message => {
        const messageEl = document.createElement('div');
        messageEl.className = `flex ${message.sent ? 'justify-end' : 'justify-start'} fade-in`;
        
        let html = '';
        
        if (message.sent) {
            html = `
                <div class="max-w-xs lg:max-w-md">
                    <div class="bg-blue-500 text-white rounded-lg px-4 py-2">
                        <p class="text-sm">${message.text}</p>
                        ${message.attachment ? `
                            <div class="mt-2 p-2 bg-blue-600 rounded border border-blue-400">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="${getFileIcon(message.attachment.type || 'application/octet-stream')}" class="w-4 h-4"></i>
                                        <div>
                                            <p class="text-xs font-medium">${message.attachment.name}</p>
                                            <p class="text-xs opacity-75">${message.attachment.size}</p>
                                        </div>
                                    </div>
                                    <button class="text-xs bg-blue-700 hover:bg-blue-800 px-2 py-1 rounded transition-colors">
                                        Descargar
                                    </button>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                    <div class="flex items-center justify-end mt-1 space-x-1">
                        <span class="text-xs text-gray-500">${message.time}</span>
                        <span class="text-xs text-gray-500">${message.read ? '✓✓' : '✓'}</span>
                    </div>
                </div>
            `;
        } else {
            html = `
                <div class="max-w-xs lg:max-w-md">
                    <div class="bg-white text-gray-900 rounded-lg px-4 py-2 border border-gray-200">
                        <p class="text-sm">${message.text}</p>
                        ${message.attachment ? `
                            <div class="mt-2 p-2 bg-gray-50 rounded border border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="${getFileIcon(message.attachment.type || 'application/octet-stream')}" class="w-4 h-4"></i>
                                        <div>
                                            <p class="text-xs font-medium">${message.attachment.name}</p>
                                            <p class="text-xs text-gray-500">${message.attachment.size}</p>
                                        </div>
                                    </div>
                                    <button class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded text-gray-700 transition-colors">
                                        Descargar
                                    </button>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">${message.time}</p>
                </div>
            `;
        }
        
        messageEl.innerHTML = html;
        messagesContainer.appendChild(messageEl);
    });
    
    // Reinicializar iconos
    lucide.createIcons();
    
    // Scroll al final
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Filtrar contactos por pestaña
function filterContacts(tab) {
    activeTab = tab;
    
    // Actualizar UI de pestañas
    tabButtons.forEach(button => {
        if (button.dataset.tab === tab) {
            button.classList.add('border-b-2', 'border-blue-500', 'text-blue-600');
            button.classList.remove('text-gray-500');
        } else {
            button.classList.remove('border-b-2', 'border-blue-500', 'text-blue-600');
            button.classList.add('text-gray-500');
        }
    });
    
    // Filtrar contactos
    const contactItems = document.querySelectorAll('.contact-item');
    contactItems.forEach(item => {
        if (tab === 'all') {
            item.classList.remove('hidden');
        } else if (tab === 'unread') {
            if (item.dataset.unread === 'true') {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        }
    });
}

// Buscar contactos
function searchContacts(query) {
    query = query.toLowerCase();
    
    const contactItems = document.querySelectorAll('.contact-item');
    contactItems.forEach(item => {
        const name = item.querySelector('.text-gray-900').textContent.toLowerCase();
        const message = item.querySelector('.text-gray-500').textContent.toLowerCase();
        
        if (name.includes(query) || message.includes(query)) {
            item.classList.remove('hidden');
        } else {
            item.classList.add('hidden');
        }
    });
}

// Enviar mensaje
function sendMessage(e) {
    e.preventDefault();
    
    const message = messageInput.value.trim();
    if (!message && selectedFiles.length === 0) return;
    if (!currentContactId) return;
    
    // Crear FormData para enviar archivos
    const formData = new FormData();
    formData.append('contact_id', currentContactId);
    if (message) formData.append('message', message);
    
    selectedFiles.forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });
    
    // Crear elemento de mensaje
    const messageEl = document.createElement('div');
    messageEl.className = 'flex justify-end fade-in';
    
    const currentTime = new Date().toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'});
    
    let attachmentHtml = '';
    if (selectedFiles.length > 0) {
        attachmentHtml = selectedFiles.map(file => `
            <div class="mt-2 p-2 bg-blue-600 rounded border border-blue-400">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="${getFileIcon(file.type)}" class="w-4 h-4"></i>
                        <div>
                            <p class="text-xs font-medium">${file.name}</p>
                            <p class="text-xs opacity-75">${formatFileSize(file.size)}</p>
                        </div>
                    </div>
                    <button class="text-xs bg-blue-700 hover:bg-blue-800 px-2 py-1 rounded transition-colors">
                        Descargar
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    messageEl.innerHTML = `
        <div class="max-w-xs lg:max-w-md">
            <div class="bg-blue-500 text-white rounded-lg px-4 py-2">
                ${message ? `<p class="text-sm">${message}</p>` : ''}
                ${attachmentHtml}
            </div>
            <div class="flex items-center justify-end mt-1 space-x-1">
                <span class="text-xs text-gray-500">${currentTime}</span>
                <span class="text-xs text-gray-500">✓</span>
            </div>
        </div>
    `;
    
    messagesContainer.appendChild(messageEl);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    // Limpiar input y archivos
    messageInput.value = '';
    clearFiles();
    
    // Reinicializar iconos
    lucide.createIcons();
    
    // Enviar a servidor
    fetch('{{ route("coordinador-general.mensajes.send") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Simular respuesta después de un tiempo aleatorio
            setTimeout(() => {
                const replyEl = document.createElement('div');
                replyEl.className = 'flex justify-start fade-in';
                
                replyEl.innerHTML = `
                    <div class="max-w-xs lg:max-w-md">
                        <div class="bg-white text-gray-900 rounded-lg px-4 py-2 border border-gray-200">
                            <p class="text-sm">${data.autoReply.text}</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">${data.autoReply.time}</p>
                    </div>
                `;
                
                messagesContainer.appendChild(replyEl);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }, 1000 + Math.random() * 2000);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Abrir modal de nuevo chat
function openNewChatModal() {
    newChatModal.classList.remove('hidden');
    newChatModal.classList.add('flex');
}

// Cerrar modal de nuevo chat
function closeNewChatModal() {
    newChatModal.classList.add('hidden');
    newChatModal.classList.remove('flex');
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar iconos
    lucide.createIcons();
    
    // Seleccionar primer contacto
    const firstContact = document.querySelector('.contact-item');
    if (firstContact) {
        const contactId = parseInt(firstContact.dataset.contactId);
        selectContact(contactId);
    }
    
    // Click en contactos
    document.querySelectorAll('.contact-item').forEach(item => {
        item.addEventListener('click', function() {
            const contactId = parseInt(this.dataset.contactId);
            selectContact(contactId);
        });
    });
    
    // Cambio de pestañas
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterContacts(this.dataset.tab);
        });
    });
    
    // Búsqueda
    searchInput.addEventListener('input', function() {
        searchContacts(this.value);
    });
    
    // Selección de archivos
    fileInput.addEventListener('change', handleFileSelect);
    
    // Envío de mensaje
    messageForm.addEventListener('submit', sendMessage);
    
    // Envío de mensaje con Enter
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage(e);
        }
    });
    
    // Nuevo chat
    newChatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("coordinador-general.mensajes.new-chat") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeNewChatModal();
                // En un entorno real, aquí actualizaríamos la lista de contactos
                // Por ahora, simplemente recargamos la página
                window.location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    });
    
    // Cerrar modal al hacer clic fuera
    newChatModal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeNewChatModal();
        }
    });
});
</script>

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
</style>
@endsection