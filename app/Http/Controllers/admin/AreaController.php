<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Repositories\AreaRepositorio;
use App\Repositories\EmpresaRepositorio;
use App\Repositories\TrabajadorRepositorio;
use App\Traits\Http\Controllers\CriterioTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
            $area->progreso = $area->porcentajeProgreso();
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
            $this->areaRepositorio->asignarCoordinador($areaCreada->id, $request->coordinador_id);

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
            $area = $this->areaRepositorio->update($id, $request->all());
            // Actualizar el coordinador si se proporciona
            if ($request->has('coordinador_id')) {
                $this->areaRepositorio->actualizarCoordinador($id, $request->coordinador_id);
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

            // Eliminar el área
            $this->areaRepositorio->delete($id);

            return redirect()->route('admin.areas.index')
                ->with('success', 'Área eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.areas.index')
                ->with('error', 'Error al eliminar el área. Inténtalo de nuevo.');
        }
    }

    public function show($id)
    {
      $area = $this->areaRepositorio->getById($id);
        if (!$area) {
            return redirect()->route('admin.areas.index')->with('error', 'Área no encontrada.');
        }

        // Si existe un coordinador activo, extraemos nombre y correo
        $area->coordinador_nombres = $area->coordinador->trabajador->nombres;
        $area->coordinador_apellido_paterno = $area->coordinador->trabajador->apellido_paterno;
        $area->coordinador_apellido_materno = $area->coordinador->trabajador->apellido_materno;
        $area->coordinador_correo = $area->coordinador->trabajador->usuario->correo; 
        $area->coordinador_id = $area->coordinador->trabajador->id ?? null;
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

    public function manage($id)
    {
        // Vista de gestión del área
        return view('admin.areas.manage', compact('id'));
    }

    public function assignCoordinator($id)
    {
        // Vista para asignar coordinador
        return view('admin.areas.assign-coordinator', compact('id'));
    }
}
