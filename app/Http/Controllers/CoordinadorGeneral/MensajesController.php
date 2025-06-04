<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use App\Repositories\MensajeRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MensajesController extends Controller
{
    protected $mensajeRepositorio;

    public function __construct(MensajeRepositorio $mensajeRepositorio)
    {
        $this->mensajeRepositorio = $mensajeRepositorio;
        // Configurar zona horaria de Lima, Perú
        config(['app.timezone' => 'America/Lima']);
        date_default_timezone_set('America/Lima');
    }

    public function index()
    {
        try {
            // Obtener el coordinador autenticado
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$trabajador) {
                return back()->with('error', 'No se encontró información del trabajador');
            }

            $coordinadorId = $trabajador->id;
            $empresaId = $trabajador->empresa_id;

            if (!$empresaId) {
                return back()->with('error', 'No se encontró la empresa asociada al trabajador');
            }

            Log::info('Cargando mensajes para coordinador', [
                'coordinador_id' => $coordinadorId,
                'empresa_id' => $empresaId
            ]);

            // Obtener conversaciones del coordinador
            $conversaciones = $this->mensajeRepositorio->getConversacionesByCoordinador($coordinadorId, $empresaId);
        
            // Obtener todos los trabajadores de la empresa para nuevos chats
            $todosTrabajadores = $this->mensajeRepositorio->getTrabajadoresDisponiblesParaChat($empresaId, $coordinadorId);
        
            // Obtener estadísticas
            $estadisticas = $this->mensajeRepositorio->getEstadisticas($coordinadorId, $empresaId);

            Log::info('Datos obtenidos para mensajería', [
                'conversaciones_count' => $conversaciones->count(),
                'trabajadores_disponibles' => $todosTrabajadores->count(),
                'mensajes_no_leidos' => $estadisticas['mensajes_no_leidos']
            ]);

            // Transformar conversaciones para la vista manteniendo el formato original
            $contacts = $conversaciones->map(function($conversacion) {
                $trabajador = $conversacion['trabajador'];
                $ultimoMensaje = $conversacion['ultimo_mensaje'];
            
                // Determinar si está en línea
                $enLinea = $trabajador->usuario && $trabajador->usuario->en_linea;
            
                // Formatear tiempo del último mensaje
                $tiempo = 'Sin mensajes';
                if ($ultimoMensaje) {
                    $fechaMensaje = Carbon::parse($ultimoMensaje->fecha . ' ' . $ultimoMensaje->hora, 'America/Lima');
                    if ($fechaMensaje->isToday()) {
                        $tiempo = $fechaMensaje->format('H:i');
                    } elseif ($fechaMensaje->isYesterday()) {
                        $tiempo = 'Ayer';
                    } else {
                        $tiempo = $fechaMensaje->format('d/m');
                    }
                }

                return [
                    'id' => $trabajador->id,
                    'name' => $trabajador->nombres . ' ' . $trabajador->apellido_paterno . ' ' . $trabajador->apellido_materno,
                    'avatar' => '/placeholder.svg?height=40&width=40',
                    'online' => $enLinea,
                    'lastMessage' => $ultimoMensaje ? $ultimoMensaje->contenido : 'Sin mensajes',
                    'time' => $tiempo,
                    'unreadCount' => $conversacion['mensajes_no_leidos'],
                    'important' => false,
                    'group' => false
                ];
            });

            // Si no hay conversaciones, mostrar algunos trabajadores disponibles
            if ($contacts->isEmpty()) {
                $contacts = $todosTrabajadores->take(5)->map(function($trabajador) {
                    return [
                        'id' => $trabajador->id,
                        'name' => $trabajador->nombres . ' ' . $trabajador->apellido_paterno . ' ' . $trabajador->apellido_materno,
                        'avatar' => '/placeholder.svg?height=40&width=40',
                        'online' => $trabajador->usuario && $trabajador->usuario->en_linea,
                        'lastMessage' => 'Inicia una conversación',
                        'time' => '',
                        'unreadCount' => 0,
                        'important' => false,
                        'group' => false
                    ];
                });
            }

            // Transformar todos los trabajadores para nuevos chats
            $allContacts = $todosTrabajadores->map(function($trabajador) {
                return [
                    'id' => $trabajador->id,
                    'name' => $trabajador->nombres . ' ' . $trabajador->apellido_paterno . ' ' . $trabajador->apellido_materno,
                    'role' => $trabajador->usuario && $trabajador->usuario->rol ? $trabajador->usuario->rol->nombre : 'Sin rol'
                ];
            });

            // No cargar mensajes inicialmente, se cargarán por AJAX
            $messages = [];

            // Estadísticas para las pestañas
            $stats = [
                'unread' => $estadisticas['mensajes_no_leidos'],
                'important' => 0
            ];

            return view('coordinador-general.mensajes.index', compact('contacts', 'allContacts', 'messages', 'stats'));

        } catch (\Exception $e) {
            Log::error('Error en mensajes index', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString()
            ]);
        
            // En caso de error, mostrar vista con datos vacíos
            return view('coordinador-general.mensajes.index', [
                'contacts' => collect([]),
                'allContacts' => collect([]),
                'messages' => [],
                'stats' => [
                    'unread' => 0,
                    'important' => 0
                ]
            ])->with('error', 'Error al cargar los mensajes: ' . $e->getMessage());
        }
    }

    public function searchWorkers(Request $request)
    {
        try {
            // Obtener el coordinador autenticado
            $user = Auth::user();
            
            if (!$user) {
                Log::error('Usuario no autenticado en searchWorkers');
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }
            
            $trabajador = $user->trabajador;
            
            if (!$trabajador) {
                Log::error('Trabajador no encontrado para usuario', ['user_id' => $user->id]);
                return response()->json(['error' => 'No se encontró información del trabajador'], 404);
            }

            $coordinadorId = $trabajador->id;
            $empresaId = $trabajador->empresa_id;
        
            if (!$empresaId) {
                Log::error('Empresa no encontrada para trabajador', ['trabajador_id' => $coordinadorId]);
                return response()->json(['error' => 'No se pudo determinar la empresa'], 404);
            }
        
            // Obtener query de forma segura
            $query = '';
            if ($request->has('query')) {
                $query = $request->input('query');
            }
            
            Log::info('Búsqueda de trabajadores iniciada', [
                'coordinador_id' => $coordinadorId,
                'empresa_id' => $empresaId,
                'query' => $query
            ]);

            // Obtener trabajadores disponibles directamente
            $trabajadores = $this->mensajeRepositorio->getTrabajadoresDisponiblesParaChat($empresaId, $coordinadorId);
            
            // Si hay query, filtrar los resultados
            if (!empty($query)) {
                $trabajadores = $trabajadores->filter(function($trabajador) use ($query) {
                    $nombreCompleto = strtolower($trabajador->nombres . ' ' . $trabajador->apellido_paterno . ' ' . $trabajador->apellido_materno);
                    return str_contains($nombreCompleto, strtolower($query));
                });
            }

            Log::info('Búsqueda de trabajadores completada', [
                'trabajadores_encontrados' => $trabajadores->count(),
                'query' => $query
            ]);

            // Transformar resultados
            $resultados = $trabajadores->map(function($trabajador) {
                return [
                    'id' => $trabajador->id,
                    'name' => $trabajador->nombres . ' ' . $trabajador->apellido_paterno . ' ' . $trabajador->apellido_materno,
                    'role' => $trabajador->usuario && $trabajador->usuario->rol ? $trabajador->usuario->rol->nombre : 'Sin rol',
                    'avatar' => '/placeholder.svg?height=40&width=40',
                    'online' => $trabajador->usuario && $trabajador->usuario->en_linea
                ];
            })->values(); // Asegurar que sea un array indexado

            return response()->json([
                'success' => true,
                'workers' => $resultados,
                'total' => $resultados->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error en búsqueda de trabajadores', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'Error en la búsqueda: ' . $e->getMessage()], 500);
        }
    }

    public function getMessages($contactId)
    {
        try {
            $user = Auth::user();
            $trabajador = $user->trabajador;
            $coordinadorId = $trabajador->id;
            $empresaId = $trabajador->empresa_id;

            // Verificar que el contacto pertenece a la empresa
            if (!$this->mensajeRepositorio->trabajadorPerteneceAEmpresa($contactId, $empresaId)) {
                return response()->json(['error' => 'Contacto no válido'], 403);
            }

            // Obtener mensajes entre el coordinador y el contacto
            $mensajes = $this->mensajeRepositorio->getMensajesEntreUsuarios($coordinadorId, $contactId);

            // Marcar mensajes como leídos
            $this->mensajeRepositorio->marcarComoLeidos($contactId, $coordinadorId);

            // Transformar mensajes para la vista
            $mensajesTransformados = $mensajes->map(function($mensaje) use ($coordinadorId) {
                $esEnviado = $mensaje->remitente_id == $coordinadorId;
                
                $fechaHora = Carbon::parse($mensaje->fecha . ' ' . $mensaje->hora, 'America/Lima');
                $tiempo = $fechaHora->format('H:i');

                $mensajeData = [
                    'id' => $mensaje->id,
                    'sent' => $esEnviado,
                    'text' => $mensaje->contenido,
                    'time' => $tiempo,
                    'read' => $mensaje->leido
                ];

                // Agregar archivo si existe
                if ($mensaje->archivo) {
                    $mensajeData['attachment'] = [
                        'name' => $mensaje->archivo->nombre ?? 'archivo',
                        'size' => $this->formatFileSize($mensaje->archivo->tamaño ?? 0),
                        'type' => $mensaje->archivo->tipo ?? 'application/octet-stream',
                        'url' => asset('storage/' . $mensaje->archivo->ruta)
                    ];
                }

                return $mensajeData;
            });

            return response()->json([
                'success' => true,
                'messages' => $mensajesTransformados
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener mensajes', [
                'error' => $e->getMessage(),
                'contactId' => $contactId
            ]);
            return response()->json(['error' => 'Error al cargar los mensajes'], 500);
        }
    }

    public function send(Request $request)
    {
        $request->validate([
            'contact_id' => 'required|integer',
            'message' => 'nullable|string|max:1000',
            'files.*' => 'nullable|file|max:10240', // 10MB por archivo
            'images.*' => 'nullable|image|max:5120' // 5MB por imagen
        ]);

        try {
            $user = Auth::user();
            $trabajador = $user->trabajador;
            $coordinadorId = $trabajador->id;
            $empresaId = $trabajador->empresa_id;

            // Verificar que el contacto pertenece a la empresa
            if (!$this->mensajeRepositorio->trabajadorPerteneceAEmpresa($request->contact_id, $empresaId)) {
                return response()->json(['error' => 'Contacto no válido'], 403);
            }

            $ahora = Carbon::now('America/Lima');
            $mensajesCreados = [];
            $ultimoMensajeTexto = '';

            // Crear mensaje de texto si existe
            if ($request->filled('message')) {
                $mensaje = $this->mensajeRepositorio->create([
                    'remitente_id' => $coordinadorId,
                    'destinatario_id' => $request->contact_id,
                    'contenido' => $request->message,
                    'fecha' => $ahora->toDateString(),
                    'hora' => $ahora->toTimeString()
                ]);
                $mensajesCreados[] = $mensaje;
                $ultimoMensajeTexto = $request->message;
            }

            // Procesar archivos
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    try {
                        // Crear archivo en la base de datos
                        $archivo = $this->mensajeRepositorio->crearArchivo($file);
                        
                        // Crear mensaje con archivo
                        $mensaje = $this->mensajeRepositorio->create([
                            'remitente_id' => $coordinadorId,
                            'destinatario_id' => $request->contact_id,
                            'contenido' => '📎 ' . $archivo->nombre,
                            'fecha' => $ahora->toDateString(),
                            'hora' => $ahora->toTimeString(),
                            'archivo_id' => $archivo->id
                        ]);
                        
                        $mensajesCreados[] = $mensaje;
                        if (empty($ultimoMensajeTexto)) {
                            $ultimoMensajeTexto = '📎 ' . $archivo->nombre;
                        }
                    } catch (\Exception $e) {
                        Log::error('Error al procesar archivo', ['error' => $e->getMessage(), 'file' => $file->getClientOriginalName()]);
                    }
                }
            }

            // Procesar imágenes
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    try {
                        // Crear archivo en la base de datos
                        $archivo = $this->mensajeRepositorio->crearArchivo($image);
                        
                        // Crear mensaje con imagen
                        $mensaje = $this->mensajeRepositorio->create([
                            'remitente_id' => $coordinadorId,
                            'destinatario_id' => $request->contact_id,
                            'contenido' => '🖼️ ' . $archivo->nombre,
                            'fecha' => $ahora->toDateString(),
                            'hora' => $ahora->toTimeString(),
                            'archivo_id' => $archivo->id
                        ]);
                        
                        $mensajesCreados[] = $mensaje;
                        if (empty($ultimoMensajeTexto)) {
                            $ultimoMensajeTexto = '🖼️ ' . $archivo->nombre;
                        }
                    } catch (\Exception $e) {
                        Log::error('Error al procesar imagen', ['error' => $e->getMessage(), 'image' => $image->getClientOriginalName()]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'messages_count' => count($mensajesCreados),
                'last_message' => $ultimoMensajeTexto,
                'time' => $ahora->format('H:i'),
                'contact_id' => $request->contact_id
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar mensaje', [
                'error' => $e->getMessage(),
                'data' => $request->except(['files', 'images'])
            ]);
            return response()->json(['error' => 'Error al enviar el mensaje'], 500);
        }
    }

    public function newChat(Request $request)
    {
        $request->validate([
            'contact_id' => 'required|integer',
            'message' => 'required|string|max:1000'
        ]);

        try {
            $user = Auth::user();
            $trabajador = $user->trabajador;
            $coordinadorId = $trabajador->id;
            $empresaId = $trabajador->empresa_id;

            // Verificar que el contacto pertenece a la empresa
            if (!$this->mensajeRepositorio->trabajadorPerteneceAEmpresa($request->contact_id, $empresaId)) {
                return response()->json(['error' => 'Contacto no válido'], 403);
            }

            $ahora = Carbon::now('America/Lima');

            // Crear el primer mensaje de la conversación
            $mensaje = $this->mensajeRepositorio->create([
                'remitente_id' => $coordinadorId,
                'destinatario_id' => $request->contact_id,
                'contenido' => $request->message,
                'fecha' => $ahora->toDateString(),
                'hora' => $ahora->toTimeString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat iniciado correctamente',
                'contact_id' => $request->contact_id
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear nuevo chat', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json(['error' => 'Error al iniciar el chat'], 500);
        }
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        try {
            $user = Auth::user();
            $trabajador = $user->trabajador;
            $empresaId = $trabajador->empresa_id;
            
            // Buscar trabajadores
            $trabajadores = $this->mensajeRepositorio->buscarTrabajadores($request->input('query'), $empresaId);

            // Transformar resultados manteniendo el formato original
            $resultados = $trabajadores->map(function($trabajador) {
                return [
                    'id' => $trabajador->id,
                    'name' => $trabajador->nombre_completo,
                    'avatar' => '/placeholder.svg?height=40&width=40',
                    'online' => $trabajador->usuario && $trabajador->usuario->en_linea,
                    'lastMessage' => 'Resultado de búsqueda',
                    'time' => '',
                    'unreadCount' => 0,
                    'important' => false,
                    'group' => false
                ];
            });

            return response()->json([
                'success' => true,
                'contacts' => $resultados
            ]);

        } catch (\Exception $e) {
            Log::error('Error en búsqueda de contactos', [
                'error' => $e->getMessage(),
                'query' => $request->input('query')
            ]);
            return response()->json(['error' => 'Error en la búsqueda'], 500);
        }
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'contact_id' => 'required|integer'
        ]);

        try {
            $user = Auth::user();
            $trabajador = $user->trabajador;
            $coordinadorId = $trabajador->id;
            $empresaId = $trabajador->empresa_id;

            // Verificar que el contacto pertenece a la empresa
            if (!$this->mensajeRepositorio->trabajadorPerteneceAEmpresa($request->contact_id, $empresaId)) {
                return response()->json(['error' => 'Contacto no válido'], 403);
            }

            // Marcar mensajes como leídos
            $this->mensajeRepositorio->marcarComoLeidos($request->contact_id, $coordinadorId);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error al marcar como leído', [
                'error' => $e->getMessage(),
                'contact_id' => $request->contact_id
            ]);
            return response()->json(['error' => 'Error al marcar como leído'], 500);
        }
    }

    private function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}
