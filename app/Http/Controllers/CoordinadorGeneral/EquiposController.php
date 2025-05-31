<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use App\Repositories\EquipoRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            // Por ahora usaremos empresa ID = 1 para pruebas
            // En producción esto vendría del usuario autenticado
            $empresaId = 1;
            
            $equipos = $this->equipoRepositorio->getAllByEmpresa($empresaId);
            $areas = $this->equipoRepositorio->getAreasDisponibles($empresaId);
            $coordinadores = $this->equipoRepositorio->getCoordinadoresDisponibles($empresaId);
            $estadisticas = $this->equipoRepositorio->getEstadisticas($empresaId);

            // Transformar datos para la vista
            $equiposTransformados = $equipos->map(function($equipo) {
                return [
                    'id' => $equipo->id,
                    'nombre' => $equipo->nombre,
                    'area' => $equipo->area->nombre,
                    'area_id' => $equipo->area->id,
                    'descripcion' => $equipo->descripcion,
                    'estado' => $equipo->estado,
                    'coordinador' => $equipo->coordinador_nombre_completo,
                    'coordinador_id' => $equipo->coordinador_id,
                    'miembros_count' => $equipo->miembros_activos_count,
                    'metas_activas' => $equipo->metas_activas_count,
                    'progreso' => $equipo->progreso_promedio,
                    'miembros' => $equipo->miembros->where('activo', true)->map(function($miembro) {
                        return $miembro->trabajador->nombre_completo;
                    })->toArray(),
                    'fecha_creacion' => $equipo->fecha_creacion ? \Carbon\Carbon::parse($equipo->fecha_creacion)->format('Y-m-d') : null
                ];
            });

            // Transformar coordinadores para la vista
            $coordinadoresTransformados = $coordinadores->map(function($coordinador) {
                return [
                    'id' => $coordinador->id,
                    'nombre' => $coordinador->nombre_completo,
                    'email' => $coordinador->usuario ? $coordinador->usuario->correo : ''
                ];
            });

            return view('coordinador-general.equipos.index', [
                'equipos' => $equiposTransformados,
                'areas' => $areas,
                'coordinadores' => $coordinadoresTransformados,
                'estadisticas' => $estadisticas
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
            $equipo = $this->equipoRepositorio->create([
                'nombre' => $request->nombre,
                'area_id' => $request->area_id,
                'coordinador_id' => $request->coordinador_id,
                'descripcion' => $request->descripcion
            ]);

            // Agregar al coordinador como miembro del equipo
            $this->equipoRepositorio->agregarMiembro($equipo->id, $request->coordinador_id);

            return redirect()->route('coordinador-general.equipos')
                           ->with('success', 'Equipo creado exitosamente');

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
                               ->with('error', 'Equipo no encontrado');
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

    public function edit($id)
    {
        try {
            $equipo = $this->equipoRepositorio->getById($id);
            
            if (!$equipo) {
                return redirect()->route('coordinador-general.equipos')
                               ->with('error', 'Equipo no encontrado');
            }

            $empresaId = $equipo->area->empresa_id;
            $areas = $this->equipoRepositorio->getAreasDisponibles($empresaId);
            $coordinadores = $this->equipoRepositorio->getCoordinadoresDisponibles($empresaId);

            return view('coordinador-general.equipos.edit', [
                'equipo' => $equipo,
                'areas' => $areas,
                'coordinadores' => $coordinadores->map(function($coordinador) {
                    return [
                        'id' => $coordinador->id,
                        'nombre' => $coordinador->nombre_completo
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error en edit', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'id' => $id]);
            return back()->with('error', 'Error al cargar el equipo: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        // Validar los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'area_id' => 'required|integer|exists:areas,id',
            'coordinador_id' => 'required|integer|exists:trabajadores,id',
            'descripcion' => 'nullable|string|max:500'
        ]);

        try {
            // Actualizar el equipo
            $actualizado = $this->equipoRepositorio->update($id, [
                'nombre' => $request->nombre,
                'area_id' => $request->area_id,
                'coordinador_id' => $request->coordinador_id,
                'descripcion' => $request->descripcion
            ]);

            if (!$actualizado) {
                return redirect()->route('coordinador-general.equipos')
                               ->with('error', 'Equipo no encontrado');
            }

            // Redirigir a la lista de equipos con mensaje de éxito
            return redirect()->route('coordinador-general.equipos')
                           ->with('success', 'Equipo actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error en update', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString(),
                'id' => $id,
                'data' => $request->all()
            ]);
            
            // En caso de error, regresar al formulario con el error
            return redirect()->route('coordinador-general.equipos.edit', $id)
                           ->with('error', 'Error al actualizar el equipo: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
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

    public function getMiembros($id)
    {
        try {
            $equipo = $this->equipoRepositorio->getById($id);
            
            if (!$equipo) {
                return response()->json(['error' => 'Equipo no encontrado'], 404);
            }

            $miembros = $equipo->miembros->where('activo', true)->map(function($miembro) use ($equipo) {
                return [
                    'id' => $miembro->trabajador->id,
                    'nombre' => $miembro->trabajador->nombre_completo,
                    'email' => $miembro->trabajador->usuario ? $miembro->trabajador->usuario->correo : '',
                    'fecha_union' => $miembro->fecha_union instanceof \Carbon\Carbon 
                        ? $miembro->fecha_union->format('Y-m-d') 
                        : \Carbon\Carbon::parse($miembro->fecha_union)->format('Y-m-d'),
                    'es_coordinador' => $miembro->trabajador->id === $equipo->coordinador_id
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
            $agregado = $this->equipoRepositorio->agregarMiembro($id, $request->trabajador_id);

            if (!$agregado) {
                return response()->json(['error' => 'No se pudo agregar el miembro o ya pertenece al equipo'], 400);
            }

            return response()->json(['success' => true, 'message' => 'Miembro agregado exitosamente']);

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
            $eliminado = $this->equipoRepositorio->removerMiembro($id, $request->trabajador_id);

            if (!$eliminado) {
                return response()->json(['error' => 'No se pudo eliminar el miembro'], 400);
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

    public function buscar(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        try {
            $empresaId = 1; // Por ahora fijo, en producción del usuario autenticado
            $equipos = $this->equipoRepositorio->buscarPorNombre($request->q, $empresaId);

            $equiposTransformados = $equipos->map(function($equipo) {
                return [
                    'id' => $equipo->id,
                    'nombre' => $equipo->nombre,
                    'area' => $equipo->area->nombre,
                    'coordinador' => $equipo->coordinador_nombre_completo,
                    'miembros_count' => $equipo->miembros_activos_count
                ];
            });

            return response()->json($equiposTransformados);

        } catch (\Exception $e) {
            Log::error('Error en buscar', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'q' => $request->q]);
            return response()->json(['error' => 'Error en la búsqueda: ' . $e->getMessage()], 500);
        }
    }
}
