<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Repositories\AreaRepositorio;
use App\Repositories\EmpresaRepositorio;
use App\Traits\Http\Controllers\CriterioTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

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
            $area->nro_equipos = $area->equipos->count();
            $area->nro_colaboradores = $area->equipos->sum(function ($equipo) {
                return $equipo->miembros->count();
            });
            $area->nro_metas_activas = $area->metasActivas->count();
            return $area;
        });
        $areasPag->setCollection($areasParse);

        return view('private.admin.areas', [
            'areas' => $areasPag,
            'criterios' => $criterios,
        ]);
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'nombre' => 'required|string|max:255|unique:areas,nombre',
    //         'codigo' => 'required|string|max:10|unique:areas,codigo',
    //         'descripcion' => 'required|string',
    //         'color' => 'required|string|in:blue,green,purple,orange,red,indigo,pink,teal',
    //         'estado' => 'required|string|in:activa,inactiva',
    //         'objetivos' => 'nullable|string',
    //         'coordinador_id' => 'nullable|exists:users,id',
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         $area = Area::create([
    //             'nombre' => $request->nombre,
    //             'codigo' => strtoupper($request->codigo),
    //             'descripcion' => $request->descripcion,
    //             'color' => $request->color,
    //             'estado' => $request->estado,
    //             'objetivos' => $request->objetivos,
    //             'coordinador_general_id' => $request->coordinador_id,
    //         ]);

    //         // Si se asignó un coordinador, actualizar el usuario
    //         if ($request->coordinador_id) {
    //             User::where('id', $request->coordinador_id)->update([
    //                 'rol' => 'coordinador_general',
    //                 'area_id' => $area->id,
    //             ]);
    //         }

    //         DB::commit();

    //         return redirect()->route('admin.areas.index')
    //             ->with('success', 'Área creada correctamente.');

    //     } catch (\Exception $e) {
    //         DB::rollback();
            
    //         return redirect()->route('admin.areas.index')
    //             ->with('error', 'Error al crear el área. Inténtalo de nuevo.');
    //     }
    // }

    // public function edit($id)
    // {
    //     // Simular datos del área para edición
    //     $area = [
    //         'id' => $id,
    //         'nombre' => 'Marketing',
    //         'codigo' => 'MKT',
    //         'descripcion' => 'Área encargada de la promoción y posicionamiento de la marca en el mercado.',
    //         'color' => 'blue',
    //         'estado' => 'activa',
    //         'objetivos' => 'Incrementar el reconocimiento de marca y generar leads cualificados.',
    //         'coordinador_id' => 1,
    //     ];

    //     return response()->json($area);
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'nombre' => 'required|string|max:255|unique:areas,nombre,' . $id,
    //         'codigo' => 'required|string|max:10|unique:areas,codigo,' . $id,
    //         'descripcion' => 'required|string',
    //         'color' => 'required|string|in:blue,green,purple,orange,red,indigo,pink,teal',
    //         'estado' => 'required|string|in:activa,inactiva',
    //         'objetivos' => 'nullable|string',
    //         'coordinador_id' => 'nullable|exists:users,id',
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         $area = Area::findOrFail($id);
    //         $coordinador_anterior = $area->coordinador_general_id;

    //         $area->update([
    //             'nombre' => $request->nombre,
    //             'codigo' => strtoupper($request->codigo),
    //             'descripcion' => $request->descripcion,
    //             'color' => $request->color,
    //             'estado' => $request->estado,
    //             'objetivos' => $request->objetivos,
    //             'coordinador_general_id' => $request->coordinador_id,
    //         ]);

    //         // Actualizar coordinador anterior si cambió
    //         if ($coordinador_anterior && $coordinador_anterior != $request->coordinador_id) {
    //             User::where('id', $coordinador_anterior)->update([
    //                 'rol' => 'colaborador',
    //                 'area_id' => null,
    //             ]);
    //         }

    //         // Asignar nuevo coordinador si se seleccionó
    //         if ($request->coordinador_id) {
    //             User::where('id', $request->coordinador_id)->update([
    //                 'rol' => 'coordinador_general',
    //                 'area_id' => $area->id,
    //             ]);
    //         }

    //         DB::commit();

    //         return redirect()->route('admin.areas.index')
    //             ->with('success', 'Área actualizada correctamente.');

    //     } catch (\Exception $e) {
    //         DB::rollback();
            
    //         return redirect()->route('admin.areas.index')
    //             ->with('error', 'Error al actualizar el área. Inténtalo de nuevo.');
    //     }
    // }

    // public function destroy($id)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $area = Area::findOrFail($id);

    //         // Verificar si tiene equipos o colaboradores asignados
    //         if ($area->equipos()->count() > 0 || $area->usuarios()->count() > 0) {
    //             return redirect()->route('admin.areas.index')
    //                 ->with('error', 'No se puede eliminar el área porque tiene equipos o colaboradores asignados.');
    //         }

    //         // Liberar coordinador si existe
    //         if ($area->coordinador_general_id) {
    //             User::where('id', $area->coordinador_general_id)->update([
    //                 'rol' => 'colaborador',
    //                 'area_id' => null,
    //             ]);
    //         }

    //         $area->delete();

    //         DB::commit();

    //         return redirect()->route('admin.areas.index')
    //             ->with('success', 'Área eliminada correctamente.');

    //     } catch (\Exception $e) {
    //         DB::rollback();
            
    //         return redirect()->route('admin.areas.index')
    //             ->with('error', 'Error al eliminar el área. Inténtalo de nuevo.');
    //     }
    // }

    public function show($id)
    {
        // Vista detallada del área
        return view('admin.areas.show', compact('id'));
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
