<?php

namespace App\Http\Controllers\colaborador;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private function defaultContacts(): array
    {
        return [
            ['id' => '1', 'name' => 'María López', 'avatar' => '/placeholder-40x40.png', 'lastMessage' => '¿Cómo va el avance del proyecto?', 'time' => '10:30', 'unreadCount' => 2, 'online' => true,  'important' => true,  'group' => false],
            ['id' => '2', 'name' => 'Carlos Rodríguez','avatar' => '/placeholder-40x40.png','lastMessage' => 'Revisemos los pendientes mañana','time' => 'Ayer','unreadCount' => 0,'online' => false,'important' => false,'group' => false],
            ['id' => '3', 'name' => 'Ana Martínez',  'avatar' => '/placeholder-40x40.png','lastMessage' => 'La reunión se cambió para el jueves','time' => 'Lun','unreadCount' => 0,'online' => true, 'important' => false,'group' => false],
            ['id' => '4', 'name' => 'Equipo Desarrollo','avatar' => '/placeholder-40x40.png','lastMessage' => 'Nueva actualización disponible','time' => '15:45','unreadCount' => 5,'online' => true, 'important' => true, 'group' => true],
            ['id' => '5', 'name' => 'Roberto Silva',  'avatar' => '/placeholder-40x40.png','lastMessage' => 'Perfecto, nos vemos entonces','time' => 'Mar','unreadCount' => 0,'online' => false,'important' => false,'group' => false],
        ];
    }

    // Mensajes simulados por contacto, almacenados en sesión
    private function getSessionKey(string $contactId): string
    {
        return "messages_contact_{$contactId}";
    }

    private function initMessages(string $contactId): array
    {
        // Inicializar con algunos mensajes de ejemplo
        return [
            ['id' => 'msg-1', 'content' => 'Hola, ¿cómo estás?', 'time' => '09:00', 'sent' => false, 'read' => true,  'files' => []],
            ['id' => 'msg-2', 'content' => 'Bien, gracias. ¿Y tú?', 'time' => '09:01', 'sent' => true,  'read' => true,  'files' => []],
        ];
    }

    public function index(Request $request): View
    {
        $contacts = $this->defaultContacts();
        $activeContactId = $request->get('contact', '1');

        // Cargar o inicializar mensajes desde sesión
        $sessionKey = $this->getSessionKey($activeContactId);
        if (! $request->session()->has($sessionKey)) {
            $request->session()->put($sessionKey, $this->initMessages($activeContactId));
        }

        $messages = $request->session()->get($sessionKey);
        $activeContact = collect($contacts)->firstWhere('id', $activeContactId);

        return view('private.colaborador.mensajes', compact(
            'contacts',
            'activeContactId',
            'messages',
            'activeContact'
        ));
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'contact_id' => 'required|string',
            'content'    => 'required|string|max:1000',
        ]);

        $contactId = $request->contact_id;
        $content   = $request->content;
        $sessionKey = $this->getSessionKey($contactId);

        // Obtener mensajes actuales
        $messages = $request->session()->get($sessionKey, []);

        // Crear mensaje enviado por el usuario
        $newMessage = [
            'id'      => 'msg-' . time(),
            'content' => $content,
            'time'    => now()->format('H:i'),
            'sent'    => true,
            'read'    => false,
            'files'   => []
        ];
        $messages[] = $newMessage;

        // Simular respuesta automática
        $responseMessage = [
            'id'      => 'msg-' . (time() + 1),
            'content' => $this->getRandomResponse(),
            'time'    => now()->addSeconds(2)->format('H:i'),
            'sent'    => false,
            'read'    => true,
            'files'   => []
        ];
        $messages[] = $responseMessage;

        // Guardar en sesión
        $request->session()->put($sessionKey, $messages);

        return response()->json([
            'success'  => true,
            'message'  => $newMessage,
            'response' => $responseMessage,
        ]);
    }

    public function fetchMessages(Request $request): JsonResponse
    {
        $request->validate([
            'contact_id' => 'required|string',
        ]);

        $contactId = $request->contact_id;
        $sessionKey = $this->getSessionKey($contactId);
        $messages = $request->session()->get($sessionKey, []);

        return response()->json(['messages' => $messages]);
    }

    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'contact_id' => 'required|string',
        ]);

        $contactId = $request->contact_id;
        $sessionKey = $this->getSessionKey($contactId);
        $messages = $request->session()->get($sessionKey, []);

        // Marcar todos los recibidos como leídos
        foreach ($messages as &$msg) {
            if (! $msg['sent']) {
                $msg['read'] = true;
            }
        }

        $request->session()->put($sessionKey, $messages);

        return response()->json(['success' => true]);
    }

    private function getRandomResponse(): string
    {
        $responses = [
            'Entendido, gracias por la información.',
            '¿Podrías darme más detalles sobre eso?',
            'Perfecto, lo tendré en cuenta.',
            '¿Necesitas que haga algo al respecto?',
            'Estoy de acuerdo con tu propuesta.',
            'Trabajaré en ello lo antes posible.',
            '¿Para cuándo necesitas esto?',
            'Voy a revisar la información y te aviso.',
            '¿Podemos discutir esto en la próxima reunión?',
            'Gracias por tu ayuda con esto.'
        ];

        return $responses[array_rand($responses)];
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
