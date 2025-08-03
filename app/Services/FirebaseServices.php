<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Illuminate\Support\Facades\Log;

class FirebaseServices
{
    private $database;
    private $firebase;

    public function __construct()
    {
        try {
            $this->firebase = (new Factory)->withServiceAccount(base_path('key/mensajeria-2b6fe-firebase-adminsdk-fbsvc-ad7accf43e.json'));
            $this->database = $this->firebase->createDatabase();
        } catch (\Exception $e) {
            Log::error('Error inicializando Firebase: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Enviar mensaje a Firebase Realtime Database
     */
    public function sendMessage($remitenteId, $destinatarioId, $mensaje, $timestamp = null)
    {
        try {
            $timestamp = $timestamp ?: now()->timestamp;
            
            // Crear ID único para el mensaje
            $messageId = $this->database->getReference('messages')->push()->getKey();
            
            // Crear estructura del mensaje
            $messageData = [
                'id' => $messageId,
                'remitente_id' => (int)$remitenteId,
                'destinatario_id' => (int)$destinatarioId,
                'contenido' => $mensaje,
                'timestamp' => $timestamp,
                'fecha' => now()->format('Y-m-d'),
                'hora' => now()->format('H:i:s'),
                'leido' => false
            ];

            // Determinar el ID de la conversación (siempre el menor ID primero)
            $conversationId = $remitenteId < $destinatarioId 
                ? "{$remitenteId}_{$destinatarioId}" 
                : "{$destinatarioId}_{$remitenteId}";

            // Guardar en Firebase
            $updates = [
                "messages/{$messageId}" => $messageData,
                "conversations/{$conversationId}/last_message" => $messageData,
                "conversations/{$conversationId}/updated_at" => $timestamp,
                "user_conversations/{$remitenteId}/{$conversationId}" => [
                    'other_user_id' => (int)$destinatarioId,
                    'updated_at' => $timestamp
                ],
                "user_conversations/{$destinatarioId}/{$conversationId}" => [
                    'other_user_id' => (int)$remitenteId,
                    'updated_at' => $timestamp,
                    'unread_count' => $this->database->getReference("user_conversations/{$destinatarioId}/{$conversationId}/unread_count")->getValue() + 1
                ]
            ];

            $this->database->getReference()->update($updates);

            Log::info('Mensaje enviado a Firebase', [
                'message_id' => $messageId,
                'conversation_id' => $conversationId,
                'remitente' => $remitenteId,
                'destinatario' => $destinatarioId
            ]);

            return $messageId;

        } catch (\Exception $e) {
            Log::error('Error enviando mensaje a Firebase: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Marcar mensajes como leídos
     */
    public function markAsRead($remitenteId, $destinatarioId)
    {
        try {
            $conversationId = $remitenteId < $destinatarioId 
                ? "{$remitenteId}_{$destinatarioId}" 
                : "{$destinatarioId}_{$remitenteId}";

            // Resetear contador de no leídos
            $this->database->getReference("user_conversations/{$destinatarioId}/{$conversationId}/unread_count")->set(0);

            Log::info('Mensajes marcados como leídos en Firebase', [
                'conversation_id' => $conversationId,
                'user_id' => $destinatarioId
            ]);

        } catch (\Exception $e) {
            Log::error('Error marcando como leído en Firebase: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener configuración de Firebase para el frontend
     */
    public function getFirebaseConfig()
    {
        return [
            'databaseURL' => env('FIREBASE_DATABASE_URL', 'https://mensajeria-2b6fe-default-rtdb.firebaseio.com/'),
            'projectId' => env('FIREBASE_PROJECT_ID', 'mensajeria-2b6fe-default-rtdb')
            
        ];
    }
}
