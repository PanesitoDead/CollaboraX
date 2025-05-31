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
            
            // Obtener solo equipos válidos (con coordinadores de equipo reales)
            $equipos = $this->equipoRepositorio->getAllByEmpresa($empresaId);
            $areas = $this->equipoRepositorio->getAreasDisponibles($empresaId);
            
            // SOLO coordinadores de equipo para crear equipos
            $coordinadores = $this->equipoRepositorio->getCoordinadoresDisponibles($empresaId);
            
            // SOLO colaboradores para agregar como miembros
            $colaboradores = $this->equipoRepositorio->getColaboradoresDisponibles($empresaId);
            
            $estadisticas = $this->equipoRepositorio->getEstadisticas($empresaId);

            // Debug: Ver qué se está obteniendo
            Log::info('Datos obtenidos', [
                'equipos_count' => $equipos->count(),
                'coordinadores_count' => $coordinadores->count(),
                'colaboradores_count' => $colaboradores->count(),
                'areas_count' => $areas->count()
            ]);

            // Transformar datos para la vista - SOLO equipos válidos
            $equiposTransformados = $equipos->map(function($equipo) {
                // Contar solo colaboradores (no coordinadores) como miembros
                $colaboradoresMiembros = $equipo->miembros->where('activo', true)->filter(function($miembro) {
                    return $miembro->trabajador->usuario && 
                           $miembro->trabajador->usuario->rol && 
                           $miembro->trabajador->usuario->rol->nombre === 'Colaborador';
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
                    'miembros_count' => $colaboradoresMiembros->count(), // Solo colaboradores
                    'metas_activas' => $equipo->metas_activas_count,
                    'progreso' => $equipo->progreso_promedio,
                    'miembros' => $colaboradoresMiembros->map(function($miembro) {
                        return $miembro->trabajador->nombre_completo;
                    })->toArray(),
                    'fecha_creacion' => $equipo->fecha_creacion ? \Carbon\Carbon::parse($equipo->fecha_creacion)->format('Y-m-d') : null
                ];
            });

            // Transformar coordinadores para la vista (SOLO coordinadores de equipo)
            $coordinadoresTransformados = $coordinadores->map(function($coordinador) {
                return [
                    'id' => $coordinador->id,
                    'nombre' => $coordinador->nombre_completo,
                    'email' => $coordinador->usuario ? $coordinador->usuario->correo : '',
                    'rol' => $coordinador->usuario && $coordinador->usuario->rol ? $coordinador->usuario->rol->nombre : ''
                ];
            });

            // Transformar colaboradores para la vista (SOLO colaboradores)
            $colaboradoresTransformados = $colaboradores->map(function($colaborador) {
                return [
                    'id' => $colaborador->id,
                    'nombre' => $colaborador->nombre_completo,
                    'email' => $colaborador->usuario ? $colaborador->usuario->correo : '',
                    'rol' => $colaborador->usuario && $colaborador->usuario->rol ? $colaborador->usuario->rol->nombre : ''
                ];
            });

            return view('coordinador-general.equipos.index', [
                'equipos' => $equiposTransformados,
                'areas' => $areas,
                'coordinadores' => $coordinadoresTransformados, // Solo coordinadores de equipo
                'colaboradores' => $colaboradoresTransformados, // Solo colaboradores
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
            // Verificar que el coordinador pertenece a la empresa
            $empresaId = 1; // En producción esto vendría del usuario autenticado
            
            if (!$this->equipoRepositorio->trabajadorPerteneceAEmpresa($request->coordinador_id, $empresaId)) {
                return back()->with('error', 'El coordinador seleccionado no pertenece a esta empresa')
                            ->withInput();
            }

            // Verificar que el trabajador es realmente un coordinador de equipo
            if (!$this->equipoRepositorio->esCoordinadorDeEquipo($request->coordinador_id, $empresaId)) {
                return back()->with('error', 'El trabajador seleccionado no es un coordinador de equipo')
                            ->withInput();
            }

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
                               ->with('error', 'Equipo no encontrado o no válido');
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
                               ->with('error', 'Equipo no encontrado o no válido');
            }

            $empresaId = $equipo->area->empresa_id;
            $areas = $this->equipoRepositorio->getAreasDisponibles($empresaId);
            
            // SOLO coordinadores de equipo
            $coordinadores = $this->equipoRepositorio->getCoordinadoresDisponibles($empresaId);

            return view('coordinador-general.equipos.edit', [
                'equipo' => $equipo,
                'areas' => $areas,
                'coordinadores' => $coordinadores->map(function($coordinador) {
                    return [
                        'id' => $coordinador->id,
                        'nombre' => $coordinador->nombre_completo,
                        'rol' => $coordinador->usuario && $coordinador->usuario->rol ? $coordinador->usuario->rol->nombre : ''
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
            // Verificar que el coordinador pertenece a la empresa
            $empresaId = 1; // En producción esto vendría del usuario autenticado
            
            if (!$this->equipoRepositorio->trabajadorPerteneceAEmpresa($request->coordinador_id, $empresaId)) {
                return redirect()->route('coordinador-general.equipos.edit', $id)
                               ->with('error', 'El coordinador seleccionado no pertenece a esta empresa')
                               ->withInput();
            }

            // Verificar que el trabajador es realmente un coordinador de equipo
            if (!$this->equipoRepositorio->esCoordinadorDeEquipo($request->coordinador_id, $empresaId)) {
                return redirect()->route('coordinador-general.equipos.edit', $id)
                               ->with('error', 'El trabajador seleccionado no es un coordinador de equipo')
                               ->withInput();
            }

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
                    'rol' => $miembro->trabajador->usuario && $miembro->trabajador->usuario->rol ? $miembro->trabajador->usuario->rol->nombre : '',
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
            // Verificar que el trabajador pertenece a la empresa
            $empresaId = 1; // En producción esto vendría del usuario autenticado
            
            if (!$this->equipoRepositorio->trabajadorPerteneceAEmpresa($request->trabajador_id, $empresaId)) {
                return response()->json(['error' => 'El trabajador seleccionado no pertenece a esta empresa'], 400);
            }

            // Verificar que el trabajador es un colaborador (no coordinador)
            if (!$this->equipoRepositorio->esColaborador($request->trabajador_id, $empresaId)) {
                return response()->json(['error' => 'Solo se pueden agregar colaboradores como miembros del equipo'], 400);
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
