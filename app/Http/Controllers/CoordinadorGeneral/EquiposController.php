<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use App\Repositories\EquipoRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $empresaId = 4;
            
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
                    'coordinador' => $equipo->coordinador->nombres . ' ' . $equipo->coordinador->apellido_paterno,
                    'coordinador_id' => $equipo->coordinador->id,
                    'miembros_count' => $equipo->miembros_activos_count,
                    'metas_activas' => $equipo->metas_activas_count,
                    'progreso' => $equipo->progreso_promedio,
                    'miembros' => $equipo->miembros->where('activo', true)->map(function($miembro) {
                        return $miembro->trabajador->nombres . ' ' . $miembro->trabajador->apellido_paterno;
                    })->toArray(),
                    'fecha_creacion' => $equipo->fecha_creacion
                ];
            });

            // Transformar coordinadores para la vista
            $coordinadoresTransformados = $coordinadores->map(function($coordinador) {
                return [
                    'id' => $coordinador->id,
                    'nombre' => $coordinador->nombres . ' ' . $coordinador->apellido_paterno . ' ' . $coordinador->apellido_materno,
                    'email' => $coordinador->usuario->correo
                ];
            });

            return view('coordinador-general.equipos.index', [
                'equipos' => $equiposTransformados,
                'areas' => $areas,
                'coordinadores' => $coordinadoresTransformados,
                'estadisticas' => $estadisticas
            ]);

        } catch (\Exception $e) {
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

            return view('coordinador-general.equipos.show', compact('equipo'));

        } catch (\Exception $e) {
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
                        'nombre' => $coordinador->nombres . ' ' . $coordinador->apellido_paterno . ' ' . $coordinador->apellido_materno
                    ];
                })
            ]);

        } catch (\Exception $e) {
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
                    'nombre' => $miembro->trabajador->nombres . ' ' . $miembro->trabajador->apellido_paterno . ' ' . $miembro->trabajador->apellido_materno,
                    'email' => $miembro->trabajador->usuario->correo,
                    'fecha_union' => $miembro->fecha_union,
                    'es_coordinador' => $miembro->trabajador->id === $equipo->coordinador_id
                ];
            })->values();

            return response()->json($miembros);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar miembros'], 500);
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
            return response()->json(['error' => 'Error al agregar miembro'], 500);
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
            return response()->json(['error' => 'Error al eliminar miembro'], 500);
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
                    'coordinador' => $equipo->coordinador->nombres . ' ' . $equipo->coordinador->apellido_paterno,
                    'miembros_count' => $equipo->miembros_activos_count
                ];
            });

            return response()->json($equiposTransformados);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la búsqueda'], 500);
        }
    }
}
