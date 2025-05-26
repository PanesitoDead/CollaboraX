{{-- resources/views/partials/contact-item.blade.php --}}
@php
    $bg = $active ? 'bg-gray-100' : '';
@endphp
<div
    class="contact-item flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors cursor-pointer {{ $bg }}"
    onclick="window.location.href='{{ route('colaborador.mensajes', ['contact' => $contact['id']]) }}'"
>
    <div class="relative h-10 w-10 rounded-full overflow-hidden">
        <img
            class="h-full w-full object-cover"
            src="{{ asset($contact['avatar']) }}"
            alt="{{ $contact['name'] }}"
        />
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
                <span
                    class="inline-flex items-center justify-center h-5 w-5 text-xs font-semibold bg-blue-600 text-white rounded-full"
                >
                    {{ $contact['unreadCount'] }}
                </span>
            @endif
        </div>
    </div>
</div>
