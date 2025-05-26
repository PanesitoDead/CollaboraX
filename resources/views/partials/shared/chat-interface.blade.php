@if($activeContact)
    <div class="bg-white rounded-lg border border-gray-300 flex flex-col h-full overflow-hidden">
        {{-- Encabezado del chat --}}
        <div class="px-4 py-3 border-b border-gray-300 flex items-center justify-between">
            <div class="flex items-center">
                <div class="relative">
                    <div class="h-10 w-10 rounded-full overflow-hidden">
                        <img
                            class="h-full w-full object-cover"
                            src="{{ asset($activeContact['avatar']) }}"
                            alt="{{ $activeContact['name'] }}"
                        />
                    </div>
                    @if($activeContact['online'])
                        <span
                            class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-green-500 border-2 border-white"
                        ></span>
                    @endif
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $activeContact['name'] }}</h3>
                    <p class="text-sm text-gray-500">
                        @if($activeContact['online'])
                            En línea
                        @else
                            {{ $activeContact['lastSeen'] ?? 'Desconectado' }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Área de mensajes --}}
        <div class="flex-1 overflow-auto px-4 py-3">
            <div id="messages-container" class="space-y-4">
                @foreach($messages as $message)
                    <div class="flex {{ $message['sent'] ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] p-4 rounded-lg {{ $message['sent'] ? 'bg-green-100 text-green-900' : 'bg-gray-100 text-gray-900' }}">
                            @if(!empty($message['files']))
                                <div class="mb-3 space-y-2">
                                    @foreach($message['files'] as $file)
                                        <div class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg p-2 text-sm">
                                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <div class="flex-grow">
                                                <div class="truncate">{{ $file['name'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $file['size'] }}</div>
                                            </div>
                                            <button
                                                class="px-3 py-1 text-xs font-medium bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            >
                                                Descargar
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="text-sm">{{ $message['content'] }}</div>
                            <div class="mt-2 flex items-center justify-end text-xs text-gray-500 space-x-1">
                                <span>{{ $message['time'] }}</span>
                                @if($message['sent'])
                                    <span>{{ $message['read'] ? '✓✓' : '✓' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Entrada de mensaje --}}
        <div class="px-4 py-3 border-t border-gray-300">
            <form id="message-form" class="flex items-center space-x-2">
                @csrf

                {{-- Adjuntar archivo --}}
                <button
                    type="button"
                    onclick="alert('Función de adjuntar archivo no implementada')"
                    class="h-10 w-10 inline-flex items-center justify-center bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                    title="Adjuntar archivo"
                >
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                </button>

                {{-- Enviar imagen --}}
                <button
                    type="button"
                    title="Enviar imagen"
                    class="h-10 w-10 inline-flex items-center justify-center bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>

                {{-- Input de texto --}}
                <input
                    id="message-input"
                    type="text"
                    placeholder="Escribe un mensaje..."
                    required
                    class="flex-1 h-10 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                />

                {{-- Emojis --}}
                <button
                    type="button"
                    title="Emojis"
                    class="h-10 w-10 inline-flex items-center justify-center bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>

                {{-- Mensaje de voz --}}
                <button
                    type="button"
                    title="Mensaje de voz"
                    class="h-10 w-10 inline-flex items-center justify-center bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                </button>

                {{-- Enviar --}}
                <button
                    type="submit"
                    class="h-10 w-10 inline-flex items-center justify-center bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
@else
    <div class="flex flex-col items-center justify-center h-full">
        <div class="text-center p-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Selecciona un chat</h3>
            <p class="text-sm text-gray-500">Elige un contacto para comenzar a chatear</p>
        </div>
    </div>
@endif
