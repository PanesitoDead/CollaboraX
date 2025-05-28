<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EquiposController extends Controller
{
    public function index()
    {
        // Datos de ejemplo para equipos
        $equipos = [
            [
                'id' => 1,
                'nombre' => 'Equipo Frontend',
                'area' => 'Desarrollo',
                'descripcion' => 'Desarrollo de interfaces de usuario',
                'estado' => 'activo',
                'coordinador' => 'Ana García',
                'miembros_count' => 5,
                'proyectos_activos' => 3,
                'progreso' => 85,
                'miembros' => ['Juan Pérez', 'María López', 'Carlos Ruiz', 'Laura Martín', 'Pedro Sánchez'],
                'created_at' => now()->subDays(30)
            ],
            [
                'id' => 2,
                'nombre' => 'Equipo Backend',
                'area' => 'Desarrollo',
                'descripcion' => 'Desarrollo de APIs y servicios',
                'estado' => 'activo',
                'coordinador' => 'Miguel Torres',
                'miembros_count' => 4,
                'proyectos_activos' => 2,
                'progreso' => 72,
                'miembros' => ['Roberto Silva', 'Carmen Díaz', 'Andrés Moreno', 'Isabel Vega'],
                'created_at' => now()->subDays(45)
            ],
            [
                'id' => 3,
                'nombre' => 'Equipo Marketing Digital',
                'area' => 'Marketing',
                'descripcion' => 'Estrategias de marketing online',
                'estado' => 'pausado',
                'coordinador' => 'Sofía Herrera',
                'miembros_count' => 6,
                'proyectos_activos' => 1,
                'progreso' => 45,
                'miembros' => ['Diego Ramírez', 'Natalia Cruz', 'Fernando Jiménez', 'Valeria Ortiz', 'Sebastián Vargas', 'Camila Rojas'],
                'created_at' => now()->subDays(15)
            ],
            [
                'id' => 4,
                'nombre' => 'Equipo Ventas',
                'area' => 'Ventas',
                'descripción' => 'Gestión de clientes y ventas',
                'estado' => 'activo',
                'coordinador' => 'Ricardo Mendoza',
                'miembros_count' => 8,
                'proyectos_activos' => 4,
                'progreso' => 90,
                'miembros' => ['Alejandra Castillo', 'Javier Guerrero', 'Mónica Restrepo', 'Fabián Delgado', 'Paola Aguilar', 'Tomás Espinoza', 'Lucía Paredes', 'Óscar Medina'],
                'created_at' => now()->subDays(60)
            ],
            [
                'id' => 5,
                'nombre' => 'Equipo Soporte Técnico',
                'area' => 'Soporte',
                'descripcion' => 'Atención al cliente y soporte',
                'estado' => 'inactivo',
                'coordinador' => 'Elena Navarro',
                'miembros_count' => 3,
                'proyectos_activos' => 0,
                'progreso' => 25,
                'miembros' => ['Raúl Figueroa', 'Adriana Salazar', 'Gonzalo Peña'],
                'created_at' => now()->subDays(90)
            ],
            [
                'id' => 6,
                'nombre' => 'Equipo UX/UI',
                'area' => 'Desarrollo',
                'descripcion' => 'Diseño de experiencia de usuario',
                'estado' => 'activo',
                'coordinador' => 'Daniela Romero',
                'miembros_count' => 4,
                'proyectos_activos' => 2,
                'progreso' => 68,
                'miembros' => ['Mateo Álvarez', 'Valentina Cortés', 'Nicolás Ramos', 'Gabriela Fuentes'],
                'created_at' => now()->subDays(20)
            ]
        ];

        // Datos de ejemplo para coordinadores
        $coordinadores = [
            ['id' => 1, 'nombre' => 'Ana García', 'email' => 'ana.garcia@empresa.com'],
            ['id' => 2, 'nombre' => 'Miguel Torres', 'email' => 'miguel.torres@empresa.com'],
            ['id' => 3, 'nombre' => 'Sofía Herrera', 'email' => 'sofia.herrera@empresa.com'],
            ['id' => 4, 'nombre' => 'Ricardo Mendoza', 'email' => 'ricardo.mendoza@empresa.com'],
            ['id' => 5, 'nombre' => 'Elena Navarro', 'email' => 'elena.navarro@empresa.com'],
            ['id' => 6, 'nombre' => 'Daniela Romero', 'email' => 'daniela.romero@empresa.com'],
            ['id' => 7, 'nombre' => 'Carlos Vásquez', 'email' => 'carlos.vasquez@empresa.com'],
            ['id' => 8, 'nombre' => 'Patricia Luna', 'email' => 'patricia.luna@empresa.com']
        ];

        return view('coordinador-general.equipos.index', compact('equipos', 'coordinadores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'area' => 'required|string',
            'coordinador_id' => 'required|integer',
            'descripcion' => 'nullable|string'
        ]);

        // Aquí normalmente guardarías en la base de datos
        // Por ahora solo redirigimos con un mensaje de éxito
        
        return redirect()->route('coordinador-general.equipos')
                        ->with('success', 'Equipo creado exitosamente');
    }

    public function show($id)
    {
        // Mostrar detalles de un equipo específico
        return view('coordinador-general.equipos.show', compact('id'));
    }

    public function edit($id)
    {
        // Mostrar formulario de edición
        return view('coordinador-general.equipos.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Actualizar equipo
        return redirect()->route('coordinador-general.equipos')
                        ->with('success', 'Equipo actualizado exitosamente');
    }

    public function destroy($id)
    {
        // Eliminar equipo
        return redirect()->route('coordinador-general.equipos')
                        ->with('success', 'Equipo eliminado exitosamente');
    }
}