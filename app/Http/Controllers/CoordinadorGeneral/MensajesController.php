<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use App\Repositories\MensajeRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MensajesController extends Controller
{
    protected $mensajeRepositorio;

    public function __construct(MensajeRepositorio $mensajeRepositorio)
    {
        $this->mensajeRepositorio = $mensajeRepositorio;
    }

    public function index()
    {
        try {
            // Por ahora usaremos empresa ID = 1 y coordinador ID = 1 para pruebas
            $empresaId = 1;
            $coordinadorId = 1; // En producción esto vendría del usuario autenticado

            // Obtener conversaciones del coordinador
            $conversaciones = $this->mensajeRepositorio->getConversacionesByCoordinador($coordinadorId, $empresaId);
            
            // Obtener todos los trabajadores de la empresa para nuevos chats
            $todosTrabajadores = $this->mensajeRepositorio->getTrabajadoresByEmpresa($empresaId);
            
            // Obtener estadísticas
            $estadisticas = $this->mensajeRepositorio->getEstadisticas($coordinadorId, $empresaId);

            // Transformar conversaciones para la vista manteniendo el formato original
            $contacts = $conversaciones->map(function($conversacion) {
                $trabajador = $conversacion['trabajador'];
                $ultimoMensaje = $conversacion['ultimo_mensaje'];
                
                // Determinar si está en línea (simulado por ahora)
                $enLinea = $trabajador->usuario && $trabajador->usuario->en_linea;
                
                // Formatear tiempo del último mensaje
                $tiempo = 'Sin mensajes';
                if ($ultimoMensaje) {
                    $fechaMensaje = \Carbon\Carbon::parse($ultimoMensaje->fecha . ' ' . $ultimoMensaje->hora);
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
                    'name' => $trabajador->nombre_completo,
                    'avatar' => '/placeholder.svg?height=40&width=40', // Por ahora placeholder
                    'online' => $enLinea,
                    'lastMessage' => $ultimoMensaje ? $ultimoMensaje->contenido : 'Sin mensajes',
                    'time' => $tiempo,
                    'unreadCount' => $conversacion['mensajes_no_leidos'],
                    'important' => false, // Por ahora no implementado
                    'group' => false // Individual chat
                ];
            });

            // Si no hay conversaciones, mostrar algunos trabajadores disponibles
            if ($contacts->isEmpty()) {
                $contacts = $todosTrabajadores->take(5)->map(function($trabajador) {
                    return [
                        'id' => $trabajador->id,
                        'name' => $trabajador->nombre_completo,
                        'avatar' => '/placeholder.svg?height=40&width=40',
                        'online' => false,
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
                    'name' => $trabajador->nombre_completo,
                    'role' => $trabajador->usuario && $trabajador->usuario->rol ? $trabajador->usuario->rol->nombre : 'Sin rol'
                ];
            });

            // Obtener mensajes reales para los contactos existentes
            $messages = [];
            foreach ($contacts as $contact) {
                $mensajesContacto = $this->mensajeRepositorio->getMensajesEntreUsuarios($coordinadorId, $contact['id'], 20);
                
                $messages[$contact['id']] = $mensajesContacto->map(function($mensaje) use ($coordinadorId) {
                    $esEnviado = $mensaje->remitente_id == $coordinadorId;
                    
                    $fechaHora = \Carbon\Carbon::parse($mensaje->fecha . ' ' . $mensaje->hora);
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
                            'size' => $mensaje->archivo->tamaño ?? 'Desconocido',
                            'type' => $mensaje->archivo->tipo ?? 'application/octet-stream'
                        ];
                    }

                    return $mensajeData;
                })->toArray();
            }

            // Estadísticas para las pestañas
            $stats = [
                'unread' => $estadisticas['mensajes_no_leidos'],
                'important' => 0 // Por ahora no implementado
            ];

            return view('coordinador-general.mensajes.index', compact('contacts', 'allContacts', 'messages', 'stats'));

        } catch (\Exception $e) {
            Log::error('Error en mensajes index', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            // En caso de error, mostrar vista con datos vacíos
            return view('coordinador-general.mensajes.index', [
                'contacts' => collect([]),
                'allContacts' => collect([]),
                'messages' => [],
                'stats' => [
                    'unread' => 0,
                    'important' => 0
                ]
            ])->with('error', 'Error al cargar los mensajes');
        }
    }

    public function getMessages($contactId)
    {
        try {
            $coordinadorId = 1; // En producción del usuario autenticado
            $empresaId = 1;

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
                
                $fechaHora = \Carbon\Carbon::parse($mensaje->fecha . ' ' . $mensaje->hora);
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
                        'size' => $mensaje->archivo->tamaño ?? 'Desconocido',
                        'type' => $mensaje->archivo->tipo ?? 'application/octet-stream'
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
            'message' => 'nullable|string|max:1000'
        ]);

        try {
            $coordinadorId = 1; // En producción del usuario autenticado
            $empresaId = 1;

            // Verificar que el contacto pertenece a la empresa
            if (!$this->mensajeRepositorio->trabajadorPerteneceAEmpresa($request->contact_id, $empresaId)) {
                return response()->json(['error' => 'Contacto no válido'], 403);
            }

            $mensaje = null;
            
            // Solo crear mensaje si hay contenido de texto
            if ($request->filled('message')) {
                // Crear el mensaje
                $mensaje = $this->mensajeRepositorio->create([
                    'remitente_id' => $coordinadorId,
                    'destinatario_id' => $request->contact_id,
                    'contenido' => $request->message
                ]);
            }

            // Manejar archivos si existen
            $attachments = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    // Aquí podrías guardar el archivo y crear un registro en la tabla archivos
                    // Por ahora solo simulamos la respuesta
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'size' => $this->formatFileSize($file->getSize()),
                        'type' => $file->getMimeType()
                    ];
                }
            }

            // Formatear respuesta
            $response = [
                'success' => true,
                'message' => [
                    'id' => $mensaje ? $mensaje->id : rand(1000, 9999),
                    'sent' => true,
                    'text' => $request->message ?? '',
                    'time' => now()->format('H:i'),
                    'read' => false
                ]
            ];

            // Agregar archivos adjuntos a la respuesta si existen
            if (!empty($attachments)) {
                $response['message']['attachments'] = $attachments;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Error al enviar mensaje', [
                'error' => $e->getMessage(),
                'data' => $request->all()
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
            $coordinadorId = 1; // En producción del usuario autenticado
            $empresaId = 1;

            // Verificar que el contacto pertenece a la empresa
            if (!$this->mensajeRepositorio->trabajadorPerteneceAEmpresa($request->contact_id, $empresaId)) {
                return response()->json(['error' => 'Contacto no válido'], 403);
            }

            // Crear el primer mensaje de la conversación
            $mensaje = $this->mensajeRepositorio->create([
                'remitente_id' => $coordinadorId,
                'destinatario_id' => $request->contact_id,
                'contenido' => $request->message
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat iniciado correctamente'
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
            $empresaId = 1;
            
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
            $coordinadorId = 1; // En producción del usuario autenticado
            $empresaId = 1;

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
