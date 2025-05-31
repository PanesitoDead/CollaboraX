<?php

namespace App\Repositories;

use App\Models\Mensaje;
use App\Models\Trabajador;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection as SupportCollection;

class MensajeRepositorio
{
    protected $model;

    public function __construct(Mensaje $model)
    {
        $this->model = $model;
    }

    /**
     * Obtener todos los trabajadores de una empresa (coordinadores y colaboradores)
     * Excluye administradores y superadministradores
     */
    public function getTrabajadoresByEmpresa(int $empresaId): Collection
    {
        return Trabajador::select('trabajadores.*')
            ->join('miembros_equipo', 'trabajadores.id', '=', 'miembros_equipo.trabajador_id')
            ->join('equipos', 'miembros_equipo.equipo_id', '=', 'equipos.id')
            ->join('areas', 'equipos.area_id', '=', 'areas.id')
            ->join('empresas', 'areas.empresa_id', '=', 'empresas.id')
            ->join('usuarios', 'trabajadores.usuario_id', '=', 'usuarios.id')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->where('empresas.id', $empresaId)
            ->where('usuarios.activo', true)
            ->where('miembros_equipo.activo', true)
            // SOLO coordinadores de equipo y colaboradores
            ->whereIn('roles.nombre', ['Coord. Equipo', 'Coordinador de Equipo', 'Colaborador'])
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('usuarios.deleted_at')
            ->whereNull('equipos.deleted_at')
            ->whereNull('areas.deleted_at')
            ->with(['usuario.rol'])
            ->distinct()
            ->orderBy('trabajadores.nombres')
            ->get();
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
        return $this->model->create([
            'remitente_id' => $data['remitente_id'],
            'destinatario_id' => $data['destinatario_id'],
            'contenido' => $data['contenido'],
            'fecha' => $data['fecha'] ?? now()->toDateString(),
            'hora' => $data['hora'] ?? now()->toTimeString(),
            'leido' => false,
            'archivo_id' => $data['archivo_id'] ?? null
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
        return Trabajador::select('trabajadores.id')
            ->join('miembros_equipo', 'trabajadores.id', '=', 'miembros_equipo.trabajador_id')
            ->join('equipos', 'miembros_equipo.equipo_id', '=', 'equipos.id')
            ->join('areas', 'equipos.area_id', '=', 'areas.id')
            ->join('empresas', 'areas.empresa_id', '=', 'empresas.id')
            ->where('trabajadores.id', $trabajadorId)
            ->where('empresas.id', $empresaId)
            ->where('miembros_equipo.activo', true)
            ->whereNull('trabajadores.deleted_at')
            ->whereNull('equipos.deleted_at')
            ->whereNull('areas.deleted_at')
            ->exists();
    }
}
