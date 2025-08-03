<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Repositories\AuditoriaRepositorio;
use App\Traits\Http\Controllers\CriterioTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Str;

class AuditoriaController extends Controller
{
    use CriterioTrait;
    
    protected AuditoriaRepositorio $auditoriaRepositorio;

    public function __construct(AuditoriaRepositorio $auditoriaRepositorio)
    {
        $this->auditoriaRepositorio = $auditoriaRepositorio;
    }

    public function index(Request $request)
    {
        $criterios = $this->obtenerCriterios($request);
        $auditoriasPag = $this->auditoriaRepositorio->obtenerConRelaciones($criterios);
        
        // Parsear los datos para la vista
        $auditoriasParse = $auditoriasPag->getCollection()->map(function ($auditoria) {
            // Formatear fecha
            $auditoria->fecha_formateada = Carbon::parse($auditoria->created_at)->format('d/m/Y H:i:s');
            
            // Usuario que realizó la acción
            $auditoria->usuario_accion = $auditoria->causer 
                ? $auditoria->causer->correo 
                : 'Sistema';
            
            // Modelo afectado (nombre corto)
            $auditoria->modelo_corto = $auditoria->subject_type 
                ? class_basename($auditoria->subject_type) 
                : 'N/A';
            
            // Color del evento
            $auditoria->color_evento = $this->getColorEvento($auditoria->event);
            
            // Descripción truncada
            $auditoria->descripcion_corta = $auditoria->description 
                ? Str::limit($auditoria->description, 50) 
                : 'Sin descripción';
            
            return $auditoria;
        });
        
        $auditoriasPag->setCollection($auditoriasParse);

        // Obtener datos para filtros
        $modelos = $this->auditoriaRepositorio->obtenerModelosDisponibles();
        $eventos = $this->auditoriaRepositorio->obtenerEventosDisponibles();
        
        return view('super-admin.auditoria', [
            'auditorias' => $auditoriasPag,
            'criterios' => $criterios,
            'modelos' => $modelos,
            'eventos' => $eventos,
        ]);
    }

    public function show($id)
    {
        $auditoria = Activity::with(['subject', 'causer'])->findOrFail($id);
        
        // Formatear datos para la vista
        $auditoria->fecha_formateada = Carbon::parse($auditoria->created_at)->format('d/m/Y H:i:s');
        $auditoria->usuario_accion = $auditoria->causer 
            ? $auditoria->causer->correo 
            : 'Sistema';
        $auditoria->modelo_corto = $auditoria->subject_type 
            ? class_basename($auditoria->subject_type) 
            : 'N/A';
        
        return view('super-admin.auditoria-detalle', [
            'auditoria' => $auditoria
        ]);
    }

    public function estadisticas()
    {
        $estadisticas = $this->auditoriaRepositorio->obtenerEstadisticas();
        $porModelo = $this->auditoriaRepositorio->obtenerPorModelo();
        $porEvento = $this->auditoriaRepositorio->obtenerPorEvento();
        
        return response()->json([
            'estadisticas' => $estadisticas,
            'por_modelo' => $porModelo,
            'por_evento' => $porEvento,
        ]);
    }

    public function limpiar(Request $request)
    {
        $request->validate([
            'dias' => 'required|integer|min:1|max:365'
        ]);

        $dias = $request->input('dias', 90);
        $eliminados = $this->auditoriaRepositorio->limpiarRegistrosAntiguos($dias);
        
        return response()->json([
            'success' => true,
            'message' => "Se eliminaron {$eliminados} registros de auditoría anteriores a {$dias} días.",
            'eliminados' => $eliminados
        ]);
    }

    private function getColorEvento(string $evento): string
    {
        return match($evento) {
            'created' => 'green',
            'updated' => 'yellow',
            'deleted' => 'red',
            default => 'gray'
        };
    }
}
