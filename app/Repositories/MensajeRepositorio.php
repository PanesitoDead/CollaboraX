<?php

namespace App\Repositories;

use App\Models\Mensaje;
use App\Models\Trabajador;
use App\Models\Usuario;
use App\Models\Archivo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection as SupportCollection;
use Carbon\Carbon;

class MensajeRepositorio
{
    protected $model;

    public function __construct(Mensaje $model)
    {
        $this->model = $model;
    }

    /**
     * Obtener todos los trabajadores de una empresa (coordinadores y colaboradores)
     * Incluye Coord. General, Coord. Equipo y Colaboradores
     */
    public function getTrabajadoresByEmpresa(int $empresaId): Collection
    {
        return Trabajador::select('trabajadores.*')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('trabajadores.empresa_id', $empresaId)
            ->where('usuarios.activo', true)
            // Incluir Coord. General, Coord. Equipo y Colaboradores
            ->whereIn('roles.nombre', ['Coord. General', 'Coord. Equipo', 'Colaborador'])
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('usuarios.deleted_at')
            ->with(['usuario.rol'])
            ->distinct()
            ->orderBy('trabajadores.nombres')
            ->get();
    }

    /**
     * Buscar trabajadores con roles específicos
     * Mejorado para manejar búsquedas vacías y filtrado por empresa
     */
    public function buscarTrabajadoresConRoles(string $query = '', int $empresaId, int $coordinadorId = null): Collection
    {
        Log::info('Iniciando búsqueda de trabajadores', [
            'query' => $query,
            'empresa_id' => $empresaId,
            'coordinador_id' => $coordinadorId
        ]);

        $queryBuilder = Trabajador::select('trabajadores.*')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('trabajadores.empresa_id', $empresaId)
            ->where('usuarios.activo', true)
            // Filtrar por roles específicos: Coord. General, Coord. Equipo, Colaborador
            ->whereIn('roles.nombre', ['Coord. General', 'Coord. Equipo', 'Colaborador'])
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('usuarios.deleted_at')
            ->with(['usuario.rol'])
            ->distinct();

        // Excluir al coordinador actual si se proporciona
        if ($coordinadorId) {
            $queryBuilder->where('trabajadores.id', '!=', $coordinadorId);
        }

        // Si hay query de búsqueda, filtrar por nombre
        if (!empty(trim($query))) {
            $queryBuilder->where(function($subQuery) use ($query) {
                $subQuery->where(DB::raw("CONCAT(trabajadores.nombres, ' ', trabajadores.apellido_paterno, ' ', trabajadores.apellido_materno)"), 'LIKE', "%{$query}%")
                        ->orWhere('trabajadores.nombres', 'LIKE', "%{$query}%")
                        ->orWhere('trabajadores.apellido_paterno', 'LIKE', "%{$query}%")
                        ->orWhere('trabajadores.apellido_materno', 'LIKE', "%{$query}%");
            });
        }

        $resultados = $queryBuilder->orderBy('trabajadores.nombres')->limit(50)->get();

        Log::info('Resultados de búsqueda de trabajadores', [
            'query' => $query,
            'empresa_id' => $empresaId,
            'coordinador_id' => $coordinadorId,
            'resultados_count' => $resultados->count(),
            'trabajadores_ids' => $resultados->pluck('id')->toArray()
        ]);

        return $resultados;
    }

    /**
     * Obtener todos los trabajadores disponibles para conversación (sin filtro de búsqueda)
     */
    public function getTrabajadoresDisponiblesParaChat(int $empresaId, int $coordinadorId): Collection
    {
        Log::info('Obteniendo trabajadores disponibles para chat', [
            'empresa_id' => $empresaId,
            'coordinador_id' => $coordinadorId
        ]);

        $trabajadores = Trabajador::select('trabajadores.*')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('trabajadores.empresa_id', $empresaId)
            ->where('trabajadores.id', '!=', $coordinadorId) // Excluir al mismo coordinador
            ->where('usuarios.activo', true)
            // Incluir Coord. General, Coord. Equipo y Colaboradores
            ->whereIn('roles.nombre', ['Coord. General', 'Coord. Equipo', 'Colaborador'])
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('usuarios.deleted_at')
            ->with(['usuario.rol'])
            ->distinct()
            ->orderBy('trabajadores.nombres')
            ->get();

        Log::info('Trabajadores disponibles obtenidos', [
            'empresa_id' => $empresaId,
            'coordinador_id' => $coordinadorId,
            'trabajadores_count' => $trabajadores->count(),
            'trabajadores_ids' => $trabajadores->pluck('id')->toArray()
        ]);

        return $trabajadores;
    }

    /**
     * Obtener conversaciones del coordinador general con otros trabajadores
     */
    public function getConversacionesByCoordinador(int $coordinadorId, int $empresaId): SupportCollection
    {
        // Obtener todos los trabajadores de la empresa
        $trabajadores = $this->getTrabajadoresByEmpresa($empresaId);
        $trabajadorIds = $trabajadores->pluck('id')->toArray();

        // Obtener las últimas conversaciones
        $conversaciones = DB::table('mensajes')
            ->select([
                DB::raw('CASE 
                    WHEN remitente_id = ' . $coordinadorId . ' THEN destinatario_id 
                    ELSE remitente_id 
                END as contacto_id'),
                DB::raw('MAX(id) as ultimo_mensaje_id'),
                DB::raw('MAX(CONCAT(fecha, " ", hora)) as ultima_actividad')
            ])
            ->where(function($query) use ($coordinadorId, $trabajadorIds) {
                $query->where('remitente_id', $coordinadorId)
                      ->whereIn('destinatario_id', $trabajadorIds);
            })
            ->orWhere(function($query) use ($coordinadorId, $trabajadorIds) {
                $query->whereIn('remitente_id', $trabajadorIds)
                      ->where('destinatario_id', $coordinadorId);
            })
            ->whereNull('deleted_at')
            ->groupBy('contacto_id')
            ->orderBy('ultima_actividad', 'desc')
            ->get();

        // Obtener los detalles de cada conversación
        $conversacionesDetalladas = collect();

        foreach ($conversaciones as $conversacion) {
            $trabajador = $trabajadores->firstWhere('id', $conversacion->contacto_id);
            if (!$trabajador) continue;

            // Obtener el último mensaje
            $ultimoMensaje = $this->model->find($conversacion->ultimo_mensaje_id);
            
            // Contar mensajes no leídos
            $mensajesNoLeidos = $this->model
                ->where('remitente_id', $conversacion->contacto_id)
                ->where('destinatario_id', $coordinadorId)
                ->where('leido', false)
                ->whereNull('deleted_at')
                ->count();

            $conversacionesDetalladas->push([
                'trabajador' => $trabajador,
                'ultimo_mensaje' => $ultimoMensaje,
                'mensajes_no_leidos' => $mensajesNoLeidos,
                'ultima_actividad' => $conversacion->ultima_actividad
            ]);
        }

        return $conversacionesDetalladas;
    }

    /**
     * Obtener mensajes entre dos trabajadores
     */
    public function getMensajesEntreUsuarios(int $usuario1Id, int $usuario2Id, int $limit = 50): Collection
    {
        return $this->model->with(['remitente', 'destinatario', 'archivo'])
            ->where(function($query) use ($usuario1Id, $usuario2Id) {
                $query->where('remitente_id', $usuario1Id)
                      ->where('destinatario_id', $usuario2Id);
            })
            ->orWhere(function($query) use ($usuario1Id, $usuario2Id) {
                $query->where('remitente_id', $usuario2Id)
                      ->where('destinatario_id', $usuario1Id);
            })
            ->whereNull('deleted_at')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Crear un nuevo mensaje
     */
    public function create(array $data): Mensaje
    {
        // Configurar zona horaria de Lima, Perú
        $ahora = Carbon::now('America/Lima');
        
        return $this->model->create([
            'remitente_id' => $data['remitente_id'],
            'destinatario_id' => $data['destinatario_id'],
            'contenido' => $data['contenido'],
            'fecha' => $data['fecha'] ?? $ahora->toDateString(),
            'hora' => $data['hora'] ?? $ahora->toTimeString(),
            'leido' => false,
            'archivo_id' => $data['archivo_id'] ?? null
        ]);
    }

    /**
     * Crear un archivo y guardarlo
     */
    public function crearArchivo($file): Archivo
    {
        // Configurar zona horaria de Lima, Perú
        $ahora = Carbon::now('America/Lima');
        
        // Generar nombre único para el archivo
        $extension = $file->getClientOriginalExtension();
        $nombreOriginal = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $nombreArchivo = $nombreOriginal . '_' . time() . '.' . $extension;
        
        // Guardar archivo en storage
        $ruta = $file->storeAs('mensajes/archivos', $nombreArchivo, 'public');
        
        // Crear registro en base de datos
        return Archivo::create([
            'nombre' => $file->getClientOriginalName(),
            'ruta' => $ruta
        ]);
    }

    /**
     * Marcar mensajes como leídos
     */
    public function marcarComoLeidos(int $remitenteId, int $destinatarioId): bool
    {
        return $this->model
            ->where('remitente_id', $remitenteId)
            ->where('destinatario_id', $destinatarioId)
            ->where('leido', false)
            ->update(['leido' => true]) > 0;
    }

    /**
     * Buscar trabajadores por nombre
     */
    public function buscarTrabajadores(string $query, int $empresaId): SupportCollection
    {
        return $this->getTrabajadoresByEmpresa($empresaId)
            ->filter(function($trabajador) use ($query) {
                $nombreCompleto = strtolower($trabajador->nombre_completo);
                $queryLower = strtolower($query);
                return strpos($nombreCompleto, $queryLower) !== false;
            });
    }

    /**
     * Obtener estadísticas de mensajes para el coordinador
     */
    public function getEstadisticas(int $coordinadorId, int $empresaId): array
    {
        $trabajadorIds = $this->getTrabajadoresByEmpresa($empresaId)->pluck('id')->toArray();

        $mensajesNoLeidos = $this->model
            ->whereIn('remitente_id', $trabajadorIds)
            ->where('destinatario_id', $coordinadorId)
            ->where('leido', false)
            ->whereNull('deleted_at')
            ->count();

        $conversacionesActivas = DB::table('mensajes')
            ->where(function($query) use ($coordinadorId, $trabajadorIds) {
                $query->where('remitente_id', $coordinadorId)
                      ->whereIn('destinatario_id', $trabajadorIds);
            })
            ->orWhere(function($query) use ($coordinadorId, $trabajadorIds) {
                $query->whereIn('remitente_id', $trabajadorIds)
                      ->where('destinatario_id', $coordinadorId);
            })
            ->whereNull('deleted_at')
            ->where('fecha', '>=', now()->subDays(7)->toDateString())
            ->select([
                DB::raw('CASE 
                    WHEN remitente_id = ' . $coordinadorId . ' THEN destinatario_id 
                    ELSE remitente_id 
                END as contacto_id')
            ])
            ->distinct()
            ->count();

        return [
            'mensajes_no_leidos' => $mensajesNoLeidos,
            'conversaciones_activas' => $conversacionesActivas,
            'total_contactos' => count($trabajadorIds)
        ];
    }

    /**
     * Verificar si un trabajador pertenece a la empresa del coordinador
     */
    public function trabajadorPerteneceAEmpresa(int $trabajadorId, int $empresaId): bool
    {
        return Trabajador::where('id', $trabajadorId)
            ->where('empresa_id', $empresaId)
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * Obtener la empresa de un trabajador
     */
    public function getEmpresaByTrabajador(int $trabajadorId): ?int
    {
        $trabajador = Trabajador::where('id', $trabajadorId)
            ->whereNull('deleted_at')
            ->first();
        
        return $trabajador ? $trabajador->empresa_id : null;
    }
}
