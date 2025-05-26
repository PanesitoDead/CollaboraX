<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = collect([
            [
                'id' => 1,
                'nombre' => 'Marketing',
                'codigo' => 'MKT',
                'descripcion' => 'Área encargada de la promoción y posicionamiento de la marca en el mercado.',
                'color' => 'blue',
                'estado' => 'activa',
                'fecha_creacion' => 'hace 2 meses',
                'coordinador' => [
                    'nombre' => 'María González',
                    'email' => 'maria.gonzalez@empresa.com',
                    'initials' => 'MG',
                ],
                'equipos' => 2,
                'colaboradores' => 8,
                'metas_activas' => 6,
                'proyectos' => 4,
                'rendimiento' => 85,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>',
            ],
            [
                'id' => 2,
                'nombre' => 'Ventas',
                'codigo' => 'VNT',
                'descripcion' => 'Área responsable de la generación de ingresos y relaciones comerciales.',
                'color' => 'green',
                'estado' => 'activa',
                'fecha_creacion' => 'hace 3 meses',
                'coordinador' => [
                    'nombre' => 'Carlos Méndez',
                    'email' => 'carlos.mendez@empresa.com',
                    'initials' => 'CM',
                ],
                'equipos' => 3,
                'colaboradores' => 12,
                'metas_activas' => 8,
                'proyectos' => 6,
                'rendimiento' => 78,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
            ],
            [
                'id' => 3,
                'nombre' => 'Operaciones',
                'codigo' => 'OPS',
                'descripcion' => 'Área encargada de los procesos operativos y logística de la empresa.',
                'color' => 'purple',
                'estado' => 'activa',
                'fecha_creacion' => 'hace 1 mes',
                'coordinador' => [
                    'nombre' => 'Ana Pérez',
                    'email' => 'ana.perez@empresa.com',
                    'initials' => 'AP',
                ],
                'equipos' => 2,
                'colaboradores' => 10,
                'metas_activas' => 5,
                'proyectos' => 3,
                'rendimiento' => 72,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>',
            ],
            [
                'id' => 4,
                'nombre' => 'Finanzas',
                'codigo' => 'FIN',
                'descripcion' => 'Área responsable de la gestión financiera y contable de la empresa.',
                'color' => 'orange',
                'estado' => 'activa',
                'fecha_creacion' => 'hace 4 meses',
                'coordinador' => [
                    'nombre' => 'Roberto García',
                    'email' => 'roberto.garcia@empresa.com',
                    'initials' => 'RG',
                ],
                'equipos' => 1,
                'colaboradores' => 6,
                'metas_activas' => 4,
                'proyectos' => 2,
                'rendimiento' => 90,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>',
            ],
            [
                'id' => 5,
                'nombre' => 'Tecnología',
                'codigo' => 'TI',
                'descripcion' => 'Área encargada del desarrollo y mantenimiento de sistemas tecnológicos.',
                'color' => 'indigo',
                'estado' => 'activa',
                'fecha_creacion' => 'hace 2 meses',
                'coordinador' => [
                    'nombre' => 'Javier López',
                    'email' => 'javier.lopez@empresa.com',
                    'initials' => 'JL',
                ],
                'equipos' => 2,
                'colaboradores' => 9,
                'metas_activas' => 7,
                'proyectos' => 5,
                'rendimiento' => 68,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>',
            ],
            [
                'id' => 6,
                'nombre' => 'Recursos Humanos',
                'codigo' => 'RH',
                'descripcion' => 'Área encargada de la gestión del talento humano y desarrollo organizacional.',
                'color' => 'pink',
                'estado' => 'inactiva',
                'fecha_creacion' => 'hace 1 semana',
                'coordinador' => null,
                'equipos' => 0,
                'colaboradores' => 0,
                'metas_activas' => 0,
                'proyectos' => 0,
                'rendimiento' => 0,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>',
            ],
        ]);

        $stats = [
            'total' => $areas->count(),
            'activas' => $areas->where('estado', 'activa')->count(),
            'equipos_total' => $areas->sum('equipos'),
            'colaboradores_total' => $areas->sum('colaboradores'),
            'colaboradores_activos' => $areas->where('estado', 'activa')->sum('colaboradores'),
            'rendimiento_promedio' => round($areas->where('estado', 'activa')->avg('rendimiento')),
        ];

        // Coordinadores disponibles para asignar
        $coordinadores_disponibles = collect([
            (object)['id' => 1, 'name' => 'Luis Martínez', 'email' => 'luis.martinez@empresa.com'],
            (object)['id' => 2, 'name' => 'Carmen Silva', 'email' => 'carmen.silva@empresa.com'],
            (object)['id' => 3, 'name' => 'Diego Ruiz', 'email' => 'diego.ruiz@empresa.com'],
        ]);

        // Simular paginación
        $areas = new \Illuminate\Pagination\LengthAwarePaginator(
            $areas,
            $areas->count(),
            10,
            1,
            ['path' => request()->url()]
        );

        return view('private.admin.areas', compact('areas', 'stats', 'coordinadores_disponibles'));
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
