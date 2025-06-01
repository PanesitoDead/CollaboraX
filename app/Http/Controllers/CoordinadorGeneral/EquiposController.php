<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use App\Repositories\EquipoRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EquiposController extends Controller
{
    protected $equipoRepositorio;

    public function __construct(EquipoRepositorio $equipoRepositorio)
    {
        $this->equipoRepositorio = $equipoRepositorio;
    }

    public function index()
    {
        try {
            // Obtener la empresa del coordinador general autenticado
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$trabajador) {
                return back()->with('error', 'No se encontró información del trabajador');
            }

            // Obtener la empresa del trabajador directamente por ID
            $empresaId = $trabajador->empresa_id;
            if (!$empresaId) {
                return back()->with('error', 'No se encontró la empresa asociada al trabajador');
            }

            $empresa = DB::table('empresas')->find($empresaId);
            if (!$empresa) {
                return back()->with('error', 'No se encontró la empresa en la base de datos');
            }
            
            // Obtener las áreas asignadas al coordinador general
            $areasCoordinador = $this->equipoRepositorio->getAreasCoordinadorGeneral($trabajador->id);
            
            if ($areasCoordinador->isEmpty()) {
                return back()->with('error', 'No tienes áreas asignadas como coordinador general');
            }

            // Obtener equipos solo de las áreas del coordinador (SOLO con coordinadores de equipo válidos)
            $equipos = $this->equipoRepositorio->getEquiposByAreas($areasCoordinador->pluck('id')->toArray());
            
            // Obtener colaboradores disponibles para convertir en coordinadores de equipo
            $colaboradores = $this->equipoRepositorio->getColaboradoresParaCoordinacion($empresaId);
            
            $estadisticas = $this->equipoRepositorio->getEstadisticasPorAreas($areasCoordinador->pluck('id')->toArray());

            // Debug: Ver qué se está obteniendo
            Log::info('Datos obtenidos para coordinador general', [
                'empresa_id' => $empresaId,
                'trabajador_id' => $trabajador->id,
                'areas_count' => $areasCoordinador->count(),
                'equipos_count' => $equipos->count(),
                'colaboradores_count' => $colaboradores->count()
            ]);

            // Transformar datos para la vista
            $equiposTransformados = $equipos->map(function($equipo) {
                // Verificar que el equipo tenga coordinadores de equipo válidos
                $coordinadoresEquipo = $equipo->miembros->where('activo', true)->filter(function($miembro) {
                    return $miembro->trabajador->usuario && 
                           $miembro->trabajador->usuario->rol && 
                           $miembro->trabajador->usuario->rol->nombre === 'Coord. Equipo';
                });

                // Solo mostrar equipos que tengan al menos un coordinador de equipo
                if ($coordinadoresEquipo->isEmpty()) {
                    return null;
                }

                $colaboradoresMiembros = $equipo->miembros->where('activo', true)->filter(function($miembro) {
                    return $miembro->trabajador->usuario && 
                           $miembro->trabajador->usuario->rol && 
                           in_array($miembro->trabajador->usuario->rol->nombre, ['Colaborador', 'Coord. Equipo']);
                });

                return [
                    'id' => $equipo->id,
                    'nombre' => $equipo->nombre,
                    'area' => $equipo->area->nombre,
                    'area_id' => $equipo->area->id,
                    'descripcion' => $equipo->descripcion,
                    'estado' => $equipo->estado,
                    'coordinador' => $equipo->coordinador_nombre_completo,
                    'coordinador_id' => $equipo->coordinador_id,
                    'miembros_count' => $colaboradoresMiembros->count(),
                    'metas_activas' => $equipo->metas_activas_count,
                    'progreso' => $equipo->progreso_promedio,
                    'miembros' => $colaboradoresMiembros->map(function($miembro) {
                        return $miembro->trabajador->nombre_completo;
                    })->toArray(),
                    'fecha_creacion' => $equipo->fecha_creacion ? \Carbon\Carbon::parse($equipo->fecha_creacion)->format('Y-m-d') : null
                ];
            })->filter(); // Filtrar elementos null

            // Transformar colaboradores para la vista
            $colaboradoresTransformados = $colaboradores->map(function($colaborador) {
                return [
                    'id' => $colaborador->id,
                    'nombre' => $colaborador->nombre_completo,
                    'email' => $colaborador->usuario ? $colaborador->usuario->correo : '',
                    'rol' => $colaborador->usuario && $colaborador->usuario->rol ? $colaborador->usuario->rol->nombre : '',
                    'area_actual' => $colaborador->area_actual ?? 'Sin área'
                ];
            });

            return view('coordinador-general.equipos.index', [
                'equipos' => $equiposTransformados,
                'areas' => $areasCoordinador,
                'colaboradores' => $colaboradoresTransformados,
                'estadisticas' => $estadisticas,
                'empresa' => $empresa
            ]);

        } catch (\Exception $e) {
            Log::error('Error en index', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Error al cargar los equipos: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'area_id' => 'required|integer|exists:areas,id',
            'coordinador_id' => 'required|integer|exists:trabajadores,id',
            'descripcion' => 'nullable|string|max:500'
        ]);

        try {
            // Obtener la empresa del coordinador general autenticado
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$trabajador) {
                return back()->with('error', 'No se encontró información del trabajador');
            }
            
            $empresaId = $trabajador->empresa_id;
            if (!$empresaId) {
                return back()->with('error', 'No se encontró la empresa asociada al trabajador');
            }
            
            // Verificar que el área pertenece al coordinador general
            if (!$this->equipoRepositorio->areaPerteneceeACoordinadorGeneral($request->area_id, $trabajador->id)) {
                return back()->with('error', 'El área seleccionada no está bajo tu coordinación')
                        ->withInput();
            }

            // Verificar que el trabajador es un colaborador
            if (!$this->equipoRepositorio->esColaboradorActivo($request->coordinador_id, $empresaId)) {
                return back()->with('error', 'El trabajador seleccionado no es un colaborador activo')
                        ->withInput();
            }

            DB::beginTransaction();

            try {
                // Crear el equipo
                $equipo = $this->equipoRepositorio->create([
                    'nombre' => $request->nombre,
                    'area_id' => $request->area_id,
                    'coordinador_id' => $request->coordinador_id,
                    'descripcion' => $request->descripcion
                ]);

                // Cambiar el rol del colaborador a "Coord. Equipo"
                $rolCambiado = $this->equipoRepositorio->cambiarRolACoordinadorEquipo($request->coordinador_id);
                if (!$rolCambiado) {
                    throw new \Exception('No se pudo cambiar el rol del trabajador a coordinador de equipo');
                }

                // Agregar al coordinador como miembro del equipo
                $miembroAgregado = $this->equipoRepositorio->agregarMiembro($equipo->id, $request->coordinador_id);
                if (!$miembroAgregado) {
                    throw new \Exception('No se pudo agregar el coordinador como miembro del equipo');
                }

                // Verificar que el equipo ahora tiene al menos un coordinador de equipo
                if (!$this->equipoRepositorio->equipoTieneCoordinadorEquipo($equipo->id)) {
                    throw new \Exception('El equipo no cumple con el criterio de tener al menos un coordinador de equipo');
                }

                DB::commit();

                Log::info('Equipo creado exitosamente con coordinador de equipo', [
                    'equipo_id' => $equipo->id,
                    'coordinador_id' => $request->coordinador_id,
                    'empresa_id' => $empresaId
                ]);

                return redirect()->route('coordinador-general.equipos')
                           ->with('success', 'Equipo creado exitosamente y coordinador asignado');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error en store', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Error al crear el equipo: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $equipo = $this->equipoRepositorio->getById($id);
            
            if (!$equipo) {
                return redirect()->route('coordinador-general.equipos')
                               ->with('error', 'Equipo no encontrado o no cumple con los criterios de validación');
            }

            // Verificar que el equipo pertenece a las áreas del coordinador general
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$this->equipoRepositorio->equipoPerteneceeACoordinadorGeneral($id, $trabajador->id)) {
                return redirect()->route('coordinador-general.equipos')
                               ->with('error', 'No tienes permisos para ver este equipo');
            }

            // Verificar y formatear fechas
            if ($equipo->fecha_creacion && !$equipo->fecha_creacion instanceof \Carbon\Carbon) {
                $equipo->fecha_creacion = \Carbon\Carbon::parse($equipo->fecha_creacion);
            }

            // Preparar los datos de las metas
            $equipo->metas->map(function($meta) {
                if ($meta->fecha_entrega && !$meta->fecha_entrega instanceof \Carbon\Carbon) {
                    $meta->fecha_entrega = \Carbon\Carbon::parse($meta->fecha_entrega);
                }
                return $meta;
            });

            return view('coordinador-general.equipos.show', compact('equipo'));

        } catch (\Exception $e) {
            Log::error('Error en show', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'id' => $id]);
            return back()->with('error', 'Error al cargar el equipo: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            // Verificar permisos
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$this->equipoRepositorio->equipoPerteneceeACoordinadorGeneral($id, $trabajador->id)) {
                return back()->with('error', 'No tienes permisos para eliminar este equipo');
            }

            $eliminado = $this->equipoRepositorio->delete($id);

            if (!$eliminado) {
                return back()->with('error', 'Equipo no encontrado');
            }

            return redirect()->route('coordinador-general.equipos')
                           ->with('success', 'Equipo eliminado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error en destroy', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'id' => $id]);
            return back()->with('error', 'Error al eliminar el equipo: ' . $e->getMessage());
        }
    }

    public function buscarColaboradores(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1'
        ]);

        try {
            $user = Auth::user();
            $trabajador = $user->trabajador;
            $empresaId = $trabajador->empresa_id;
            
            $colaboradores = $this->equipoRepositorio->buscarColaboradores($request->q, $empresaId);

            $colaboradoresTransformados = $colaboradores->map(function($colaborador) {
                return [
                    'id' => $colaborador->id,
                    'nombre' => $colaborador->nombre_completo,
                    'email' => $colaborador->usuario ? $colaborador->usuario->correo : '',
                    'rol' => $colaborador->usuario && $colaborador->usuario->rol ? $colaborador->usuario->rol->nombre : '',
                    'area_actual' => $colaborador->area_actual ?? 'Sin área'
                ];
            });

            return response()->json($colaboradoresTransformados);

        } catch (\Exception $e) {
            Log::error('Error en buscarColaboradores', ['error' => $e->getMessage(), 'q' => $request->q]);
            return response()->json(['error' => 'Error en la búsqueda: ' . $e->getMessage()], 500);
        }
    }

    public function getMiembros($id)
    {
        try {
            // Verificar permisos
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$this->equipoRepositorio->equipoPerteneceeACoordinadorGeneral($id, $trabajador->id)) {
                return response()->json(['error' => 'No tienes permisos para ver este equipo'], 403);
            }

            $equipo = $this->equipoRepositorio->getById($id);
            
            if (!$equipo) {
                return response()->json(['error' => 'Equipo no encontrado o no válido'], 404);
            }

            $miembros = $equipo->miembros->where('activo', true)->map(function($miembro) use ($equipo) {
                return [
                    'id' => $miembro->trabajador->id,
                    'nombre' => $miembro->trabajador->nombre_completo,
                    'email' => $miembro->trabajador->usuario ? $miembro->trabajador->usuario->correo : '',
                    'rol' => $miembro->trabajador->usuario && $miembro->trabajador->usuario->rol ? $miembro->trabajador->usuario->rol->nombre : '',
                    'fecha_union' => $miembro->fecha_union instanceof \Carbon\Carbon 
                        ? $miembro->fecha_union->format('Y-m-d') 
                        : \Carbon\Carbon::parse($miembro->fecha_union)->format('Y-m-d'),
                    'es_coordinador' => $miembro->trabajador->id === $equipo->coordinador_id,
                    'es_coordinador_equipo' => $miembro->trabajador->usuario && $miembro->trabajador->usuario->rol && $miembro->trabajador->usuario->rol->nombre === 'Coord. Equipo'
                ];
            })->values();

            return response()->json($miembros);

        } catch (\Exception $e) {
            Log::error('Error en getMiembros', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'id' => $id]);
            return response()->json(['error' => 'Error al cargar miembros: ' . $e->getMessage()], 500);
        }
    }

    public function agregarMiembro(Request $request, $id)
    {
        $request->validate([
            'trabajador_id' => 'required|integer|exists:trabajadores,id'
        ]);

        try {
            // Verificar permisos
            $user = Auth::user();
            $trabajador = $user->trabajador;
            $empresaId = $trabajador->empresa_id;
            
            if (!$this->equipoRepositorio->equipoPerteneceeACoordinadorGeneral($id, $trabajador->id)) {
                return response()->json(['error' => 'No tienes permisos para modificar este equipo'], 403);
            }

            // Verificar que el trabajador es un colaborador activo
            if (!$this->equipoRepositorio->esColaboradorActivo($request->trabajador_id, $empresaId)) {
                return response()->json(['error' => 'Solo se pueden agregar colaboradores activos como miembros del equipo'], 400);
            }

            $agregado = $this->equipoRepositorio->agregarMiembro($id, $request->trabajador_id);

            if (!$agregado) {
                return response()->json(['error' => 'No se pudo agregar el miembro o ya pertenece al equipo'], 400);
            }

            return response()->json(['success' => true, 'message' => 'Colaborador agregado exitosamente']);

        } catch (\Exception $e) {
            Log::error('Error en agregarMiembro', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString(),
                'id' => $id,
                'trabajador_id' => $request->trabajador_id
            ]);
            return response()->json(['error' => 'Error al agregar miembro: ' . $e->getMessage()], 500);
        }
    }

    public function eliminarMiembro(Request $request, $id)
    {
        $request->validate([
            'trabajador_id' => 'required|integer|exists:trabajadores,id'
        ]);

        try {
            // Verificar permisos
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$this->equipoRepositorio->equipoPerteneceeACoordinadorGeneral($id, $trabajador->id)) {
                return response()->json(['error' => 'No tienes permisos para modificar este equipo'], 403);
            }

            $eliminado = $this->equipoRepositorio->removerMiembro($id, $request->trabajador_id);

            if (!$eliminado) {
                return response()->json(['error' => 'No se pudo eliminar el miembro o es el último coordinador de equipo'], 400);
            }

            return response()->json(['success' => true, 'message' => 'Miembro eliminado exitosamente']);

        } catch (\Exception $e) {
            Log::error('Error en eliminarMiembro', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString(),
                'id' => $id,
                'trabajador_id' => $request->trabajador_id
            ]);
            return response()->json(['error' => 'Error al eliminar miembro: ' . $e->getMessage()], 500);
        }
    }
}
