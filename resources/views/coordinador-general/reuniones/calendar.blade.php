<div class="rounded-md border border-gray-200">
    <div class="grid grid-cols-7 gap-px border-b bg-gray-100 p-2 text-center text-sm font-medium">
        <div class="p-2">Lun</div>
        <div class="p-2">Mar</div>
        <div class="p-2">Mié</div>
        <div class="p-2">Jue</div>
        <div class="p-2">Vie</div>
        <div class="p-2">Sáb</div>
        <div class="p-2">Dom</div>
    </div>
    <div class="grid grid-cols-7 gap-px bg-gray-200">
        @for($i = 0; $i < 35; $i++)
            @php
                $day = $i - 3 + 1; // Ajuste para que el mes comience en el día correcto
                $isCurrentMonth = $day > 0 && $day <= 31;
                $hasEvent = in_array($day, [10, 15, 20, 25, 28, 30]);
                $isToday = $day === 20;
            @endphp
            <div class="min-h-[100px] bg-white p-2 {{ $isToday ? 'bg-blue-50' : '' }} {{ !$isCurrentMonth ? 'text-gray-400 opacity-50' : '' }}">
                @if($isCurrentMonth)
                    <div class="text-right text-sm">{{ $day }}</div>
                    @if($hasEvent)
                        <div class="mt-2">
                            @if($day === 15)
                                <div class="mb-1 rounded bg-purple-100 p-1 text-xs text-purple-800">
                                    10:00 Revisión Estrategia
                                </div>
                                <div class="rounded bg-blue-100 p-1 text-xs text-blue-800">
                                    16:00 Presentación Resultados
                                </div>
                            @elseif($day === 10)
                                <div class="mb-1 rounded bg-purple-100 p-1 text-xs text-purple-800">
                                    14:00 Análisis Campañas
                                </div>
                            @elseif($day === 20)
                                <div class="mb-1 rounded bg-purple-100 p-1 text-xs text-purple-800">
                                    09:00 Capacitación
                                </div>
                            @elseif($day === 25)
                                <div class="mb-1 rounded bg-purple-100 p-1 text-xs text-purple-800">
                                    11:00 Optimización Procesos
                                </div>
                            @elseif($day === 28)
                                <div class="mb-1 rounded bg-purple-100 p-1 text-xs text-purple-800">
                                    10:00 Revisión Objetivos
                                </div>
                                <div class="rounded bg-blue-100 p-1 text-xs text-blue-800">
                                    15:00 Reunión Directiva
                                </div>
                            @elseif($day === 30)
                                <div class="mb-1 rounded bg-purple-100 p-1 text-xs text-purple-800">
                                    14:00 Estrategia Marketing
                                </div>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        @endfor
    </div>
</div>