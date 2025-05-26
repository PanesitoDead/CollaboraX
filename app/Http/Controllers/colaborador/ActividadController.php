<?php

namespace App\Http\Controllers\colaborador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->get('search', '');
        
        // Datos de ejemplo - en producción vendrían de la base de datos
        $actividades = collect([
            [
                'id' => 1,
                'title' => 'Diseñar mockups para la nueva landing page',
                'description' => 'Crear mockups de alta fidelidad para la nueva landing page del producto X. Incluir versiones para móvil y escritorio con todos los elementos visuales y de navegación necesarios.',
                'due_date' => '2023-06-20',
                'status' => 'en-proceso',
                'priority' => 'alta',
                'team' => 'Diseño Gráfico',
                'goal' => 'Lanzar nueva página de producto',
                'goal_id' => 'goal-002',
                'team_id' => 'team-002',
                'assigned_by' => 'María González',
            ],
            [
                'id' => 2,
                'title' => 'Optimizar imágenes para el blog',
                'description' => 'Optimizar todas las imágenes del blog para mejorar el tiempo de carga. Usar formato WebP y comprimir sin pérdida de calidad visible. Incluir alt text para SEO.',
                'due_date' => '2023-06-15',
                'status' => 'incompleta',
                'priority' => 'media',
                'team' => 'Diseño Gráfico',
                'goal' => 'Aumentar tráfico web en un 25%',
                'goal_id' => 'goal-001',
                'team_id' => 'team-001',
                'assigned_by' => 'Carlos Méndez',
            ],
            [
                'id' => 3,
                'title' => 'Crear contenido para redes sociales',
                'description' => 'Desarrollar 10 piezas de contenido para Instagram y Facebook sobre el lanzamiento del nuevo producto. Incluir copy, hashtags y programación de publicaciones.',
                'due_date' => '2023-06-10',
                'status' => 'suspendida',
                'priority' => 'alta',
                'team' => 'Marketing Digital',
                'goal' => 'Lanzar campaña en redes sociales',
                'goal_id' => 'goal-003',
                'team_id' => 'team-001',
                'assigned_by' => 'Carlos Méndez',
            ],
            [
                'id' => 4,
                'title' => 'Actualizar documentación técnica',
                'description' => 'Actualizar la documentación técnica del API con los nuevos endpoints y parámetros. Incluir ejemplos de uso y casos de error.',
                'due_date' => '2023-06-25',
                'status' => 'completa',
                'priority' => 'media',
                'team' => 'Desarrollo Web',
                'goal' => 'Mejorar experiencia de desarrolladores',
                'goal_id' => 'goal-004',
                'team_id' => 'team-003',
                'assigned_by' => 'Ana López',
            ],
            [
                'id' => 5,
                'title' => 'Preparar reporte mensual de métricas',
                'description' => 'Compilar y analizar las métricas de marketing del mes anterior. Preparar presentación para la reunión del equipo con insights y recomendaciones.',
                'due_date' => '2023-06-15',
                'status' => 'completa',
                'priority' => 'alta',
                'team' => 'Marketing Digital',
                'goal' => 'Aumentar tráfico web en un 25%',
                'goal_id' => 'goal-001',
                'team_id' => 'team-001',
                'assigned_by' => 'Carlos Méndez',
            ],
        ]);

        // Filtrar actividades por búsqueda
        if ($searchQuery) {
            $actividades = $actividades->filter(function ($actividad) use ($searchQuery) {
                return str_contains(strtolower($actividad['title']), strtolower($searchQuery)) ||
                       str_contains(strtolower($actividad['description']), strtolower($searchQuery)) ||
                       str_contains(strtolower($actividad['team']), strtolower($searchQuery));
            });
        }

        // Agrupar por estado para el kanban
        $kanbanColumns = [
            [
                'id' => '',
                'title' => 'Incompletas',
                'status' => 'incompleta',
                'color' => 'text-yellow-600',
                'items' => $actividades->where('status', 'incompleta')->values(),
            ],
            [
                'id' => 'en-proceso',
                'title' => 'En Proceso',
                'status' => 'en-proceso',
                'color' => 'text-blue-600',
                'items' => $actividades->where('status', 'en-proceso')->values(),
            ],
            [
                'id' => 'completas',
                'title' => 'Completas',
                'status' => 'completa',
                'color' => 'text-green-600',
                'items' => $actividades->where('status', 'completa')->values(),
            ],
            [
                'id' => 'suspendidas',
                'title' => 'Suspendidas',
                'status' => 'suspendida',
                'color' => 'text-red-600',
                'items' => $actividades->where('status', 'suspendida')->values(),
            ],
        ];

        return view('private.colaborador.actividades', compact('kanbanColumns', 'searchQuery', 'actividades'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:incompleta,en-proceso,completa,suspendida'
        ]);

        // En producción, actualizar en la base de datos
        // Activity::where('id', $id)->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        // En producción, buscar en la base de datos
        $actividades = collect([
            [
                'id' => 'act-001',
                'title' => 'Diseñar mockups para la nueva landing page',
                'description' => 'Crear mockups de alta fidelidad para la nueva landing page del producto X. Incluir versiones para móvil y escritorio con todos los elementos visuales y de navegación necesarios.',
                'due_date' => '2023-06-20',
                'status' => 'en-proceso',
                'priority' => 'alta',
                'team' => 'Diseño Gráfico',
                'goal' => 'Lanzar nueva página de producto',
                'goal_id' => 'goal-002',
                'team_id' => 'team-002',
                'assigned_by' => 'María González',
            ],
            // ... otros datos
        ]);

        $actividad = $actividades->firstWhere('id', $id);

        if (!$actividad) {
            abort(404);
        }

        return response()->json($actividad);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
