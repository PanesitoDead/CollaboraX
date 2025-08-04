<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use App\Services\FirebaseServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MensajesCoordinadorController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseServices $firebaseService)
    {
        $this->firebaseService = $firebaseService;
        config(['app.timezone' => 'America/Lima']);
        date_default_timezone_set('America/Lima');
    }

    public function index()
    {
        try {
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

            // Solo obtener trabajadores disponibles de MySQL para el selector
            $todosTrabajadores = \App\Models\Trabajador::where('empresa_id', $empresaId)
                ->where('id', '!=', $coordinadorId)
                ->with(['usuario.rol', 'usuario.fotoPerfil'])
                ->get();

            $allWorkers = $todosTrabajadores->map(function($trabajador) {
                return [
                    'id' => $trabajador->id,
                    'name' => $trabajador->nombres . ' ' . $trabajador->apellido_paterno . ' ' . $trabajador->apellido_materno,
                    'role' => $trabajador->usuario && $trabajador->usuario->rol ? $trabajador->usuario->rol->nombre : 'Sin rol',
                    'avatar' => optional(optional($trabajador->usuario)->fotoPerfil)->ruta ? asset('storage/' . $trabajador->usuario->fotoPerfil->ruta) : '/placeholder.svg?height=40&width=40',
                    'online' => $trabajador->usuario && $trabajador->usuario->en_linea
                ];
            });

            // Los contactos, mensajes y estadísticas ahora se cargarán desde Firebase via JavaScript
            $contacts = collect([]);
            $messages = [];
            $stats = [
                'unread' => 0,
                'important' => 0
            ];

            // Obtener configuración de Firebase para el frontend
            $firebaseConfig = $this->firebaseService->getFirebaseConfig();

            return view('private.coord-equipo.mensajes', compact('contacts', 'allWorkers', 'messages', 'stats', 'firebaseConfig'));

        } catch (\Exception $e) {
            Log::error('Error en mensajes index', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString()
            ]);
        
            return view('private.coord-equipo.mensajes', [
                'contacts' => collect([]),
                'allWorkers' => collect([]),
                'messages' => [],
                'stats' => [
                    'unread' => 0,
                    'important' => 0
                ],
                'firebaseConfig' => $this->firebaseService->getFirebaseConfig()
            ])->with('error', 'Error al cargar los mensajes: ' . $e->getMessage());
        }
    }

    public function send(Request $request)
    {
        $request->validate([
            'contact_id' => 'required|integer',
            'message' => 'nullable|string|max:1000',
            'files.*' => 'nullable|file|max:10240',
            'images.*' => 'nullable|image|max:5120'
        ]);

        try {
            $user = Auth::user();
            $trabajador = $user->trabajador;
            $coordinadorId = $trabajador->id;
            $empresaId = $trabajador->empresa_id;

            // Verificar que el contacto pertenece a la misma empresa
            $contacto = \App\Models\Trabajador::where('id', $request->contact_id)
                ->where('empresa_id', $empresaId)
                ->first();

            if (!$contacto) {
                return response()->json(['error' => 'Contacto no válido'], 403);
            }

            $ahora = Carbon::now('America/Lima');
            $ultimoMensajeTexto = '';

            // Process text message only if it exists and no files/images are included
            if ($request->filled('message') && !$request->hasFile('files') && !$request->hasFile('images')) {
                $ultimoMensajeTexto = $request->message;

                $this->firebaseService->sendMessage(
                    $coordinadorId,
                    $request->contact_id,
                    $request->message,
                    $ahora->timestamp,
                    null, // attachmentUrl
                    null, // attachmentType
                    false // isRead: false por defecto
                );
            }

            // Process files
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    try {
                        $filePath = $file->store('public/mensajes/archivos');
                        $fileUrl = asset(str_replace('public/', 'storage/', $filePath));
                        $contenidoArchivo = '📎 ' . $file->getClientOriginalName();

                        if (empty($ultimoMensajeTexto)) {
                            $ultimoMensajeTexto = $contenidoArchivo;
                        }

                        $this->firebaseService->sendMessage(
                            $coordinadorId,
                            $request->contact_id,
                            $request->filled('message') ? $request->message : $contenidoArchivo,
                            $ahora->timestamp,
                            $fileUrl,
                            $file->getMimeType(),
                            false // isRead: false por defecto
                        );
                    } catch (\Exception $e) {
                        Log::error('Error al procesar archivo', ['error' => $e->getMessage(), 'file' => $file->getClientOriginalName()]);
                    }
                }
            }

            // Process images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    try {
                        $imagePath = $image->store('public/mensajes/imagenes');
                        $imageUrl = asset(str_replace('public/', 'storage/', $imagePath));
                        $contenidoImagen = '🖼️ ' . $image->getClientOriginalName();

                        if (empty($ultimoMensajeTexto)) {
                            $ultimoMensajeTexto = $contenidoImagen;
                        }

                        $this->firebaseService->sendMessage(
                            $coordinadorId,
                            $request->contact_id,
                            $request->filled('message') ? $request->message : $contenidoImagen,
                            $ahora->timestamp,
                            $imageUrl,
                            $image->getMimeType(),
                            false // isRead: false por defecto
                        );
                    } catch (\Exception $e) {
                        Log::error('Error al procesar imagen', ['error' => $e->getMessage(), 'image' => $image->getClientOriginalName()]);
                    }
                }
            }

            return response()->json([
                'success' => true,
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

            // Verificar que el contacto pertenece a la misma empresa
            $contacto = \App\Models\Trabajador::where('id', $request->contact_id)
                ->where('empresa_id', $empresaId)
                ->first();

            if (!$contacto) {
                return response()->json(['error' => 'Contacto no válido'], 403);
            }

            // Marcar como leídos solo en Firebase
            $this->firebaseService->markAsRead($request->contact_id, $coordinadorId);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error al marcar como leído', [
                'error' => $e->getMessage(),
                'contact_id' => $request->contact_id
            ]);
            return response()->json(['error' => 'Error al marcar como leído'], 500);
        }
    }

    public function searchWorkers(Request $request)
    {
        try {
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
        
            $query = $request->input('query', '');
            
            Log::info('Búsqueda de trabajadores iniciada', [
                'coordinador_id' => $coordinadorId,
                'empresa_id' => $empresaId,
                'query' => $query
            ]);

            $trabajadores = \App\Models\Trabajador::where('empresa_id', $empresaId)
                ->where('id', '!=', $coordinadorId)
                ->with(['usuario.rol', 'usuario.fotoPerfil'])
                ->get();
            
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

            $resultados = $trabajadores->map(function($trabajador) {
                return [
                    'id' => $trabajador->id,
                    'name' => $trabajador->nombres . ' ' . $trabajador->apellido_paterno . ' ' . $trabajador->apellido_materno,
                    'role' => $trabajador->usuario && $trabajador->usuario->rol ? $trabajador->usuario->rol->nombre : 'Sin rol',
                    'avatar' => optional(optional($trabajador->usuario)->fotoPerfil)->ruta ? asset('storage/' . $trabajador->usuario->fotoPerfil->ruta) : '/placeholder.svg?height=40&width=40',
                    'online' => $trabajador->usuario && $trabajador->usuario->en_linea
                ];
            })->values();

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

    // ELIMINADO: getMessages - ahora se carga directamente desde Firebase

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

            // Verificar que el contacto pertenece a la misma empresa
            $contacto = \App\Models\Trabajador::where('id', $request->contact_id)
                ->where('empresa_id', $empresaId)
                ->first();

            if (!$contacto) {
                return response()->json(['error' => 'Contacto no válido'], 403);
            }

            $ahora = Carbon::now('America/Lima');

            // Enviar solo a Firebase, con leido: false inicialmente
            $this->firebaseService->sendMessage(
                $coordinadorId,
                $request->contact_id,
                $request->message,
                $ahora->timestamp,
                null, // attachmentUrl
                null, // attachmentType
                false // isRead: false por defecto para nuevos mensajes
            );

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
            $coordinadorId = $trabajador->id;
            
            $trabajadores = \App\Models\Trabajador::where('empresa_id', $empresaId)
                ->where('id', '!=', $coordinadorId)
                ->with(['usuario.fotoPerfil'])
                ->get();

            $query = $request->input('query');
            $trabajadores = $trabajadores->filter(function($trabajador) use ($query) {
                $nombreCompleto = strtolower($trabajador->nombres . ' ' . $trabajador->apellido_paterno . ' ' . $trabajador->apellido_materno);
                return str_contains($nombreCompleto, strtolower($query));
            });

            $resultados = $trabajadores->map(function($trabajador) {
                return [
                    'id' => $trabajador->id,
                    'name' => $trabajador->nombres . ' ' . $trabajador->apellido_paterno . ' ' . $trabajador->apellido_materno,
                    'avatar' => optional(optional($trabajador->usuario)->fotoPerfil)->ruta ? asset('storage/' . $trabajador->usuario->fotoPerfil->ruta) : '/placeholder.svg?height=40&width=40',
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

    private function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}
