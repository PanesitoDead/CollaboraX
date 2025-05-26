@extends('layouts.private.colaborador')

@section('title', 'Mensajes')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col h-[calc(100vh-4rem)]">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold tracking-tight">Mensajes</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1">
            <!-- Lista de contactos -->
            <div class="md:col-span-1">
                @include('partials.shared.contact-list', [
                    'contacts' => $contacts,
                    'activeContactId' => $activeContactId
                ])
            </div>

            <!-- Interfaz de chat -->
            <div class="md:col-span-2">
                @include('partials.shared.chat-interface', [
                    'messages' => $messages,
                    'activeContact' => $activeContact,
                    'activeContactId' => $activeContactId
                ])
            </div>
        </div>
    </div>

    <!-- Toast notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const messagesContainer = document.getElementById('messages-container');
    const searchInput = document.getElementById('search-input');
    const tabButtons = document.querySelectorAll('[data-tab]');
    const contactItems = document.querySelectorAll('.contact-item');

    // Enviar mensaje
    if (messageForm) {
        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const content = messageInput.value.trim();
            if (!content) return;

            const contactId = '{{ $activeContactId }}';
            
            try {
                const response = await fetch('{{ route("colaborador.mensajes") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        contact_id: contactId,
                        content: content
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Agregar mensaje enviado
                    addMessageToChat(data.message);
                    messageInput.value = '';
                    
                    // Simular respuesta después de un delay
                    setTimeout(() => {
                        addMessageToChat(data.response);
                    }, 1000 + Math.random() * 2000);
                }
            } catch (error) {
                console.error('Error sending message:', error);
                showToast('Error al enviar mensaje', 'error');
            }
        });
    }

    // Búsqueda de contactos
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            filterContacts(query);
        });
    }

    // Tabs de contactos
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tab = this.dataset.tab;
            
            // Actualizar tabs activos
            tabButtons.forEach(btn => btn.classList.remove('bg-background', 'text-foreground', 'shadow-sm'));
            this.classList.add('bg-background', 'text-foreground', 'shadow-sm');
            
            // Filtrar contactos por tab
            filterContactsByTab(tab);
        });
    });

    // Funciones auxiliares
    function addMessageToChat(message) {
        const messageElement = createMessageElement(message);
        messagesContainer.appendChild(messageElement);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function createMessageElement(message) {
        const div = document.createElement('div');
        div.className = `flex ${message.sent ? 'justify-end' : 'justify-start'}`;
        
        div.innerHTML = `
            <div class="max-w-[80%] rounded-lg p-3 ${message.sent ? 'bg-primary text-primary-foreground' : 'bg-muted'}">
                ${message.files && message.files.length > 0 ? createFilesHtml(message.files, message.sent) : ''}
                <div class="text-sm">${escapeHtml(message.content)}</div>
                <div class="mt-1 flex items-center justify-end gap-1 text-xs opacity-70">
                    <span>${message.time}</span>
                    ${message.sent ? `<span>${message.read ? '✓✓' : '✓'}</span>` : ''}
                </div>
            </div>
        `;
        
        return div;
    }

    function createFilesHtml(files, sent) {
        return `
            <div class="mb-2 space-y-2">
                ${files.map(file => `
                    <div class="flex items-center gap-2 rounded bg-background/10 p-2 text-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        <div class="flex-grow truncate">
                            <div class="truncate">${escapeHtml(file.name)}</div>
                            <div class="text-xs opacity-70">${escapeHtml(file.size)}</div>
                        </div>
                        <button class="h-7 text-xs px-2 py-1 rounded ${sent ? 'bg-secondary text-secondary-foreground' : 'border border-border bg-background'} hover:opacity-80">
                            Descargar
                        </button>
                    </div>
                `).join('')}
            </div>
        `;
    }

    function filterContacts(query) {
        contactItems.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const lastMessage = item.dataset.lastMessage.toLowerCase();
            
            if (name.includes(query) || lastMessage.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function filterContactsByTab(tab) {
        contactItems.forEach(item => {
            const unreadCount = parseInt(item.dataset.unreadCount) || 0;
            const important = item.dataset.important === 'true';
            
            let show = false;
            
            switch(tab) {
                case 'all':
                    show = true;
                    break;
                case 'unread':
                    show = unreadCount > 0;
                    break;
                case 'important':
                    show = important;
                    break;
            }
            
            item.style.display = show ? 'block' : 'none';
        });
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `p-4 rounded-md shadow-lg ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'} transform transition-transform duration-300 translate-x-full`;
        toast.textContent = message;
        
        document.getElementById('toast-container').appendChild(toast);
        
        setTimeout(() => toast.classList.remove('translate-x-full'), 100);
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Auto-scroll al final de los mensajes
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
</script>
@endpush
@endsection