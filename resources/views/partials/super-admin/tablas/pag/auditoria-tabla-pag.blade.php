<div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-300">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha/Hora</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($auditorias as $auditoria)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $auditoria->fecha_formateada }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-xs font-medium">
                                        {{ $auditoria->causer ? strtoupper(substr($auditoria->usuario_accion, 0, 2)) : 'SY' }}
                                    </span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $auditoria->usuario_accion }}</p>
                                    @if($auditoria->causer)
                                        <p class="text-xs text-gray-500">ID: {{ $auditoria->causer_id }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{
                                $auditoria->color_evento == 'green' ? 'bg-green-100 text-green-800' : (
                                $auditoria->color_evento == 'yellow' ? 'bg-yellow-100 text-yellow-800' : (
                                $auditoria->color_evento == 'red' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'
                                ))
                            }}">
                                {{ ucfirst($auditoria->event) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $auditoria->modelo_corto }}</code>
                                @if($auditoria->subject_id)
                                    <div class="text-xs text-gray-500 mt-1">ID: {{ $auditoria->subject_id }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $auditoria->descripcion_corta }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('super-admin.auditoria.show', $auditoria->id) }}" 
                               class="inline-flex items-center px-3 py-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-full transition-colors">
                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                Ver Detalle
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <i data-lucide="search-x" class="w-12 h-12 text-gray-400"></i>
                                <p class="text-gray-500 text-lg font-medium">No se encontraron registros de auditoría</p>
                                <p class="text-gray-400 text-sm">Intenta ajustar los filtros de búsqueda</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($auditorias->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-300 sm:px-6">
            <div class="flex justify-between items-center">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if ($auditorias->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-gray-100 border border-gray-300 cursor-default leading-5 rounded-md">
                            Anterior
                        </span>
                    @else
                        <a href="{{ $auditorias->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-blue-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                            Anterior
                        </a>
                    @endif

                    @if ($auditorias->hasMorePages())
                        <a href="{{ $auditorias->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-blue-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                            Siguiente
                        </a>
                    @else
                        <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-gray-100 border border-gray-300 cursor-default leading-5 rounded-md">
                            Siguiente
                        </span>
                    @endif
                </div>

                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 leading-5">
                            Mostrando
                            <span class="font-medium">{{ $auditorias->firstItem() }}</span>
                            a
                            <span class="font-medium">{{ $auditorias->lastItem() }}</span>
                            de
                            <span class="font-medium">{{ $auditorias->total() }}</span>
                            registros
                        </p>
                    </div>

                    <div>
                        <span class="relative z-0 inline-flex shadow-sm rounded-md">
                            {{-- Previous Page Link --}}
                            @if ($auditorias->onFirstPage())
                                <span aria-disabled="true" aria-label="Anterior">
                                    <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-gray-100 border border-gray-300 cursor-default rounded-l-md leading-5" aria-hidden="true">
                                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                                    </span>
                                </span>
                            @else
                                <a href="{{ $auditorias->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-blue-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Anterior">
                                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                                </a>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($auditorias->getUrlRange(1, $auditorias->lastPage()) as $page => $url)
                                @if ($page == $auditorias->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-blue-600 border border-blue-600 cursor-default leading-5">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-blue-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Ir a la página {{ $page }}">{{ $page }}</a>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($auditorias->hasMorePages())
                                <a href="{{ $auditorias->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-blue-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Siguiente">
                                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                                </a>
                            @else
                                <span aria-disabled="true" aria-label="Siguiente">
                                    <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-gray-100 border border-gray-300 cursor-default rounded-r-md leading-5" aria-hidden="true">
                                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                                    </span>
                                </span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
