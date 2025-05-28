<!-- Estado vacío -->
<div id="empty-chat" class="bg-white rounded-lg shadow-sm border h-full flex items-center justify-center">
    <div class="text-center">
        <i data-lucide="message-circle" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Selecciona una conversación</h3>
        <p class="text-gray-500">Elige un contacto para comenzar a chatear</p>
    </div>
</div>

<!-- Interfaz de chat -->
<div id="chat-interface" class="bg-white rounded-lg shadow-sm border h-full flex flex-col hidden">
    <!-- Header del chat -->
    <div id="chat-header" class="p-4 border-b bg-gray-50 rounded-t-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img id="contact-avatar" class="h-10 w-10 rounded-full" src="/placeholder.svg" alt="">
                <div>
                    <h3 id="contact-name" class="text-lg font-medium text-gray-900"></h3>
                    <p id="contact-status" class="text-sm text-gray-500"></p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                    <i data-lucide="phone" class="h-5 w-5"></i>
                </button>
                <button class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                    <i data-lucide="video" class="h-5 w-5"></i>
                </button>
                <button class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                    <i data-lucide="more-vertical" class="h-5 w-5"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Área de mensajes -->
    <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-4">
        <!-- Los mensajes se cargarán dinámicamente aquí -->
    </div>
    
    <!-- Input de mensaje -->
    <div class="p-4 border-t bg-gray-50 rounded-b-lg">
        <div class="flex items-end space-x-2">
            <button class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                <i data-lucide="paperclip" class="h-5 w-5"></i>
            </button>
            <div class="flex-1">
                <textarea id="message-input" 
                         rows="1" 
                         class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm resize-none" 
                         placeholder="Escribe un mensaje..."></textarea>
            </div>
            <button onclick="sendMessage()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i data-lucide="send" class="h-4 w-4"></i>
            </button>
        </div>
    </div>
</div>

<script>
// Actualizar header del chat cuando se selecciona un contacto
function updateChatHeader(contactId) {
    const contacts = @json($contacts);
    const contact = contacts.find(c => c.id === contactId);
    
    if (contact) {
        document.getElementById('contact-avatar').src = contact.avatar;
        document.getElementById('contact-avatar').alt = contact.name;
        document.getElementById('contact-name').textContent = contact.name;
        document.getElementById('contact-status').textContent = contact.online ? 'En línea' : 'Desconectado';
    }
}

// Sobrescribir la función selectContact para incluir actualización del header
const originalSelectContact = selectContact;
selectContact = function(contactId) {
    originalSelectContact(contactId);
    updateChatHeader(contactId);
};
</script>