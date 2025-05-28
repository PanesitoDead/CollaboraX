<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MensajesController extends Controller
{
    public function index()
    {
        // Datos de contactos con conversaciones
        $contacts = [
            [
                'id' => 1,
                'name' => 'Laura Sánchez',
                'avatar' => '/placeholder.svg?height=40&width=40',
                'online' => true,
                'lastMessage' => '¿Podemos revisar los avances del proyecto?',
                'time' => '10:30',
                'unreadCount' => 3,
                'important' => false
            ],
            [
                'id' => 2,
                'name' => 'Carlos Méndez',
                'avatar' => '/placeholder.svg?height=40&width=40',
                'online' => false,
                'lastMessage' => 'Ya terminé las tareas asignadas',
                'time' => '09:15',
                'unreadCount' => 0,
                'important' => false
            ],
            [
                'id' => 3,
                'name' => 'Ana Rodríguez',
                'avatar' => '/placeholder.svg?height=40&width=40',
                'online' => true,
                'lastMessage' => 'Necesito ayuda con el informe',
                'time' => 'Ayer',
                'unreadCount' => 1,
                'important' => true
            ],
            [
                'id' => 4,
                'name' => 'Grupo de Diseño',
                'avatar' => '/placeholder.svg?height=40&width=40',
                'online' => false,
                'lastMessage' => 'Miguel: Compartí los nuevos mockups',
                'time' => 'Ayer',
                'unreadCount' => 0,
                'important' => true
            ],
            [
                'id' => 5,
                'name' => 'Javier López',
                'avatar' => '/placeholder.svg?height=40&width=40',
                'online' => false,
                'lastMessage' => '¿Cuándo es la próxima reunión?',
                'time' => 'Lun',
                'unreadCount' => 0,
                'important' => false
            ],
            [
                'id' => 6,
                'name' => 'Equipo de Desarrollo',
                'avatar' => '/placeholder.svg?height=40&width=40',
                'online' => false,
                'lastMessage' => 'Tú: Revisemos el sprint backlog',
                'time' => 'Dom',
                'unreadCount' => 0,
                'important' => false
            ],
        ];

        // Todos los contactos disponibles para nuevo chat
        $allContacts = [
            [
                'id' => 7,
                'name' => 'María González',
                'role' => 'Diseñadora'
            ],
            [
                'id' => 8,
                'name' => 'Pedro Ramírez',
                'role' => 'Desarrollador'
            ],
            [
                'id' => 9,
                'name' => 'Sofía Torres',
                'role' => 'Product Manager'
            ],
            [
                'id' => 10,
                'name' => 'Alejandro Díaz',
                'role' => 'QA Tester'
            ]
        ];

        // Mensajes por contacto
        $messages = [
            // Laura Sánchez
            1 => [
                [
                    'id' => 101,
                    'sent' => true,
                    'text' => 'Hola Laura, ¿cómo vas con las tareas asignadas?',
                    'time' => '10:15',
                    'read' => true
                ],
                [
                    'id' => 102,
                    'sent' => false,
                    'text' => 'Hola! Estoy avanzando bien, ya terminé la documentación del API.',
                    'time' => '10:17',
                    'read' => true
                ],
                [
                    'id' => 103,
                    'sent' => true,
                    'text' => 'Excelente. ¿Necesitas ayuda con algo más?',
                    'time' => '10:18',
                    'read' => true
                ],
                [
                    'id' => 104,
                    'sent' => false,
                    'text' => 'Sí, tengo algunas dudas sobre el diseño de la base de datos. Te envío el diagrama que hice.',
                    'time' => '10:20',
                    'attachment' => [
                        'name' => 'diagrama-db.pdf',
                        'size' => '2.4 MB'
                    ],
                    'read' => true
                ],
                [
                    'id' => 105,
                    'sent' => true,
                    'text' => 'Gracias, lo revisaré y te doy feedback. Mientras tanto, aquí tienes la documentación actualizada del proyecto.',
                    'time' => '10:25',
                    'attachment' => [
                        'name' => 'documentacion-proyecto-v2.docx',
                        'size' => '1.8 MB'
                    ],
                    'read' => true
                ],
                [
                    'id' => 106,
                    'sent' => false,
                    'text' => '¿Podemos tener una reunión rápida para discutir los cambios en el sprint?',
                    'time' => '10:30',
                    'read' => false
                ]
            ],
            // Carlos Méndez
            2 => [
                [
                    'id' => 201,
                    'sent' => true,
                    'text' => 'Hola Carlos, ¿cómo va todo con el proyecto de marketing?',
                    'time' => '09:00',
                    'read' => true
                ],
                [
                    'id' => 202,
                    'sent' => false,
                    'text' => 'Todo bien, acabo de terminar las tareas asignadas para esta semana.',
                    'time' => '09:10',
                    'read' => true
                ],
                [
                    'id' => 203,
                    'sent' => true,
                    'text' => 'Excelente trabajo. ¿Podrías enviarme un resumen de los resultados?',
                    'time' => '09:12',
                    'read' => true
                ],
                [
                    'id' => 204,
                    'sent' => false,
                    'text' => 'Ya terminé las tareas asignadas. Te envío el informe por correo.',
                    'time' => '09:15',
                    'read' => true
                ]
            ],
            // Ana Rodríguez
            3 => [
                [
                    'id' => 301,
                    'sent' => false,
                    'text' => 'Necesito ayuda con el informe trimestral, ¿tienes un momento?',
                    'time' => 'Ayer 15:30',
                    'read' => true
                ],
                [
                    'id' => 302,
                    'sent' => true,
                    'text' => 'Claro, ¿qué necesitas específicamente?',
                    'time' => 'Ayer 15:45',
                    'read' => true
                ],
                [
                    'id' => 303,
                    'sent' => false,
                    'text' => 'No entiendo cómo calcular las métricas de rendimiento según el nuevo formato',
                    'time' => 'Ayer 16:00',
                    'read' => true
                ]
            ]
        ];

        // Estadísticas para las pestañas
        $stats = [
            'unread' => 2, // Número de conversaciones no leídas
            'important' => 2 // Número de conversaciones importantes
        ];

        return view('coordinador-general.mensajes.index', compact('contacts', 'allContacts', 'messages', 'stats'));
    }

    public function send(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'contact_id' => 'required|integer',
            'message' => 'required|string'
        ]);

        // En un entorno real, aquí guardaríamos el mensaje en la base de datos
        // y lo enviaríamos a través de un sistema de mensajería en tiempo real

        // Simulamos una respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => [
                'id' => rand(1000, 9999),
                'sent' => true,
                'text' => $request->message,
                'time' => now()->format('H:i'),
                'read' => false
            ],
            'autoReply' => [
                'id' => rand(1000, 9999),
                'sent' => false,
                'text' => $this->generateAutoReply(),
                'time' => now()->addMinutes(1)->format('H:i'),
                'read' => true
            ]
        ]);
    }

    public function newChat(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'contact_id' => 'required|integer',
            'message' => 'required|string'
        ]);

        // En un entorno real, aquí crearíamos una nueva conversación
        // y enviaríamos el primer mensaje

        // Simulamos una respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Chat iniciado correctamente'
        ]);
    }

    public function search(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        // En un entorno real, aquí buscaríamos en la base de datos
        // Simulamos una respuesta con resultados filtrados
        $query = strtolower($request->query('query'));
        
        // Datos de contactos con conversaciones (mismos que en index)
        $contacts = [
            [
                'id' => 1,
                'name' => 'Laura Sánchez',
                'avatar' => '/placeholder.svg?height=40&width=40',
                'online' => true,
                'lastMessage' => '¿Podemos revisar los avances del proyecto?',
                'time' => '10:30',
                'unreadCount' => 3,
                'important' => false
            ],
            // ... otros contactos
        ];
        
        // Filtrar contactos que coincidan con la búsqueda
        $filteredContacts = array_filter($contacts, function($contact) use ($query) {
            return stripos(strtolower($contact['name']), $query) !== false || 
                   stripos(strtolower($contact['lastMessage']), $query) !== false;
        });
        
        return response()->json([
            'success' => true,
            'contacts' => array_values($filteredContacts)
        ]);
    }

    private function generateAutoReply()
    {
        $replies = [
            'Gracias por tu mensaje. Lo revisaré pronto.',
            'Recibido. Te responderé en cuanto pueda.',
            'Entendido, trabajaré en ello.',
            'Perfecto, gracias por la información.',
            '¿Podríamos agendar una reunión para discutir esto?',
            'Necesito más detalles sobre este tema.',
            'Excelente, continuemos con el plan.',
            'Voy a consultar con el equipo y te aviso.',
            'Esto es justo lo que necesitábamos.',
            'Déjame verificar y te confirmo.'
        ];

        return $replies[array_rand($replies)];
    }
}