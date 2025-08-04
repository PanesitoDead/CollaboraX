<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\AreaRepositorio;
use App\Repositories\EmpresaRepositorio;
use App\Traits\Http\Controllers\CriterioTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    use CriterioTrait;

    protected AreaRepositorio $areaRepositorio;
    protected EmpresaRepositorio $empresaRepositorio;

    public function __construct(AreaRepositorio $areaRepositorio, EmpresaRepositorio $empresaRepositorio)
    {
        $this->areaRepositorio = $areaRepositorio;
        $this->empresaRepositorio = $empresaRepositorio;
    }

    public function index(Request $request)
    {
        $usuario = Auth::user();
        $empresa = $this->empresaRepositorio->findOneBy('usuario_id', $usuario->id);
        $criterios = $this->obtenerCriterios($request);
        // Creamos el query builder para las áreas
        $query = $this->areaRepositorio->getModel()->newQuery();
        // Filtramos por la empresa del usuario autenticado
        $query->where('empresa_id', $empresa->id);
        // Aplicamos los criterios de búsqueda
        $areasPag = $this->areaRepositorio->obtenerPaginado($criterios, $query);
        $areasParse = $areasPag->getCollection()->map(function ($area) {
            // Si existe un coordinador activo, extraemos nombre y correo
            if ($area->coordinador && $area->coordinador->trabajador) {
                $area->coordinador_nombres = $area->coordinador->trabajador->nombres;
                $area->coordinador_apellido_paterno = $area->coordinador->trabajador->apellido_paterno;
                $area->coordinador_apellido_materno = $area->coordinador->trabajador->apellido_materno;
                $area->coordinador_correo = $area->coordinador->trabajador->usuario->correo; 
            } else {
                $area->coordinador_nombre = null;
                $area->coordinador_correo = null;
            }
            $area->nro_equipos = $area->equipos->count();
            $area->nro_colaboradores = $area->equipos->sum(function ($equipo) {
                return $equipo->miembros->count();
            });
            $area->progreso = $area->porcentajeProgreso;
            $area->nro_metas_activas = $area->metasActivas->count();
            $area->fecha_creacion = $area->fecha_creacion
                ? Carbon::parse($area->fecha_creacion)->format('d/m/Y')
                : 'No disponible';
            return $area;
        });
        $areasPag->setCollection($areasParse);

        return view('private.admin.areas', [
            'areas' => $areasPag,
            'criterios' => $criterios,
        ]);
    }

    public function store(Request $request)
    {   
        try {
            // Validar los datos de entrada
            $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:1000',
            ]);

            $usuario = Auth::user();
            $empresa = $this->empresaRepositorio->findOneBy('usuario_id', $usuario->id);

            // Crear el área con los datos validados
            $areaCreada = $this->areaRepositorio->create([
                'nombre'        => $request->nombre,
                'descripcion'   => $request->descripcion,
                'empresa_id'    => $empresa->id,
                'codigo'        => $request->codigo,
                'color'         => $request->color,
                'activo'        => $request->activo,
                'fecha_creacion'=> now(),
            ]);

            // Asignar el coordinador si se proporciona
            if ($request->coordinador_id !== null) {
                $this->areaRepositorio->actualizarCoordinador($areaCreada->id, $request->coordinador_id);
            }
            
            return redirect()->route('admin.areas.index')
                ->with('success', 'Área creada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.areas.index')
                ->with('error', 'Error al crear el área. Inténtalo de nuevo. ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $area = $this->areaRepositorio->findOneBy('id', $id);
            if (!$area) {
                return redirect()->route('admin.areas.index')
                    ->with('error', 'Área no encontrada.');
            }

            // Actualizar el área con los datos validados
            $this->areaRepositorio->update($id, $request->all());
            // Actualizar el coordinador si se proporciona
            if ($request->coordinador_id !== null) {
                $this->areaRepositorio->actualizarCoordinador($area->id, $request->coordinador_id);
            }

            return redirect()->route('admin.areas.index')
                ->with('success', 'Área actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.areas.index')
                ->with('error', 'Error al actualizar el área. Inténtalo de nuevo.');
        }
    }

    public function destroy($id)
    {
        try {
            $area = $this->areaRepositorio->findOneBy('id', $id);
            if (!$area) {
                return redirect()->route('admin.areas.index')
                    ->with('error', 'Área no encontrada.');
            }

            // Buscar coordinadores generales de esta área y cambiar su rol a colaborador
            $coordinadoresGenerales = DB::table('areas_coordinador as ac')
                ->join('trabajadores as t', 't.id', '=', 'ac.trabajador_id')
                ->join('usuarios as u', 'u.id', '=', 't.usuario_id')
                ->join('roles as r', 'r.id', '=', 'u.rol_id')
                ->where('ac.area_id', $id)
                ->where('r.nombre', 'Coord. General')
                ->whereNull('ac.deleted_at')
                ->whereNull('t.deleted_at')
                ->whereNull('u.deleted_at')
                ->select('u.id as usuario_id')
                ->get();

            // Obtener el rol de colaborador
            $rolColaborador = DB::table('roles')
                ->where('nombre', 'Colaborador')
                ->first();

            if (!$rolColaborador) {
                return redirect()->route('admin.areas.index')
                    ->with('error', 'No se encontró el rol de Colaborador en el sistema.');
            }

            // Cambiar el rol de los coordinadores generales a colaborador
            foreach ($coordinadoresGenerales as $coordinador) {
                DB::table('usuarios')
                    ->where('id', $coordinador->usuario_id)
                    ->update(['rol_id' => $rolColaborador->id]);
            }

            // Eliminar el área
            $this->areaRepositorio->delete($id);

            $mensaje = 'Área eliminada correctamente';
            if ($coordinadoresGenerales->count() > 0) {
                $mensaje .= '. Se han cambiado ' . $coordinadoresGenerales->count() . ' coordinador(es) general(es) a colaborador(es)';
            }

            return redirect()->route('admin.areas.index')
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            return redirect()->route('admin.areas.index')
                ->with('error', 'Error al eliminar el área: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $area = $this->areaRepositorio->getById($id);
        // Verificamos si el área existe
        
        if (!$area) {
            return redirect()->route('admin.areas.index')->with('error', 'Área no encontrada.');
        }
        if ($area->coordinador && $area->coordinador->trabajador) {
                $area->coordinador_nombres = $area->coordinador->trabajador->nombres;
                $area->coordinador_apellido_paterno = $area->coordinador->trabajador->apellido_paterno;
                $area->coordinador_apellido_materno = $area->coordinador->trabajador->apellido_materno;
                $area->coordinador_correo = $area->coordinador->trabajador->usuario->correo;
        }
       
        // Agregamos el campo nro_equipos
        $area->nro_equipos = $area->equipos->count();
        // Agregamos el campo nro_colaboradores
        $area->nro_colaboradores = $area->equipos->sum(function ($equipo) {
            return $equipo->miembros->count();
        });
        // Agregamos el campo nro_metas_activas
        $area->nro_metas_activas = $area->metasActivas->count();
        // Parseamos la fecha de creación
        $area->fecha_creacion = $area->fecha_creacion
            ? Carbon::parse($area->fecha_creacion)->format('d/m/Y')
            : 'No disponible';
        return response()->json($area);
    }

    /**
     * Validar campo para edición de área
     */
    public function validarCampoEdicion(Request $request)
    {
        $campo = $request->input('campo');
        $valor = $request->input('valor');
        $areaId = $request->input('area_id');

        // Validaciones básicas
        if (empty($valor)) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Este campo es obligatorio.'
            ]);
        }

        switch ($campo) {
            case 'nombre':
                if (strlen($valor) < 2) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El nombre del área debe tener al menos 2 caracteres.'
                    ]);
                }
                if (strlen($valor) > 100) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El nombre del área no puede exceder 100 caracteres.'
                    ]);
                }
                if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\-\_\.]+$/', $valor)) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El nombre solo puede contener letras, números, espacios, guiones y puntos.'
                    ]);
                }

                // Verificar si ya existe otra área con el mismo nombre
                $empresa = $this->getEmpresa();
                if ($empresa) {
                    // Buscar área con el mismo nombre en la misma empresa, excluyendo el área actual
                    $query = $this->areaRepositorio->getModel()->newQuery();
                    $query->where('nombre', $valor)
                          ->where('empresa_id', $empresa->id);
                    
                    if ($areaId) {
                        $query->where('id', '!=', $areaId);
                    }
                    
                    $existente = $query->first();
                    
                    if ($existente) {
                        return response()->json([
                            'valido' => false,
                            'mensaje' => 'Ya existe un área con este nombre en la empresa.'
                        ]);
                    }
                }
                break;

            case 'codigo':
                if (strlen($valor) < 2) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El código del área debe tener al menos 2 caracteres.'
                    ]);
                }
                if (strlen($valor) > 10) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El código del área no puede exceder 10 caracteres.'
                    ]);
                }
                if (!preg_match('/^[A-Z0-9\-\_]+$/', $valor)) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'El código solo puede contener letras mayúsculas, números, guiones y guiones bajos.'
                    ]);
                }

                // Verificar si ya existe otra área con el mismo código
                $empresa = $this->getEmpresa();
                if ($empresa) {
                    // Buscar área con el mismo código en la misma empresa, excluyendo el área actual
                    $query = $this->areaRepositorio->getModel()->newQuery();
                    $query->where('codigo', $valor)
                          ->where('empresa_id', $empresa->id);
                    
                    if ($areaId) {
                        $query->where('id', '!=', $areaId);
                    }
                    
                    $existente = $query->first();
                    
                    if ($existente) {
                        return response()->json([
                            'valido' => false,
                            'mensaje' => 'Ya existe un área con este código en la empresa.'
                        ]);
                    }
                }
                break;

            case 'descripcion':
                if (strlen($valor) > 500) {
                    return response()->json([
                        'valido' => false,
                        'mensaje' => 'La descripción no puede exceder 500 caracteres.'
                    ]);
                }
                break;

            default:
                return response()->json([
                    'valido' => false,
                    'mensaje' => 'Campo no válido para validación.'
                ]);
        }

        return response()->json([
            'valido' => true,
            'mensaje' => 'Campo válido.'
        ]);
    }

    /**
     * Obtener la empresa del usuario autenticado
     */
    private function getEmpresa()
    {
        $usuario = Auth::user();
        return $this->empresaRepositorio->findOneBy('usuario_id', $usuario->id);
    }
}
