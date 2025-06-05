<?php

namespace App\Http\Controllers\CoordinadorGeneral;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Trabajador;
use App\Models\Archivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class ConfigurationController extends Controller
{
    /**
     * Display the configuration page.
     */
    public function index()
    {
        try {
            // Obtener el usuario autenticado (igual que en DashboardController)
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$trabajador) {
                return back()->with('error', 'No se encontró información del trabajador');
            }

            Log::info('Usuario y trabajador encontrados', [
                'user_id' => $user->id,
                'trabajador_id' => $trabajador->id,
                'nombres' => $trabajador->nombres
            ]);

            // Buscar foto de perfil si existe
            $fotoPerfil = null;
            if ($user->foto) {
                $fotoPerfil = Archivo::find($user->foto);
                Log::info('Foto de perfil', [
                    'foto_id' => $user->foto,
                    'archivo_encontrado' => $fotoPerfil ? 'Si' : 'No'
                ]);
            }

            // Preparar datos para la vista
            $userData = [
                'nombres' => $trabajador->nombres ?? '',
                'apellido_paterno' => $trabajador->apellido_paterno ?? '',
                'apellido_materno' => $trabajador->apellido_materno ?? '',
                'correo' => $user->correo ?? '',
                'telefono' => $trabajador->telefono ?? '',
                'doc_identidad' => $trabajador->doc_identidad ?? '',
                'fecha_nacimiento' => $trabajador->fecha_nacimiento ? date('Y-m-d', strtotime($trabajador->fecha_nacimiento)) : '',
                'foto_url' => $fotoPerfil ? asset('storage/' . $fotoPerfil->ruta) : null,
                'iniciales' => $this->getIniciales($trabajador->nombres, $trabajador->apellido_paterno)
            ];

            Log::info('Datos preparados para vista de configuración', [
                'user_id' => $user->id,
                'trabajador_id' => $trabajador->id,
                'tiene_foto' => $fotoPerfil ? 'Si' : 'No'
            ]);
            
            return view('coordinador-general.configuracion.index', compact('userData'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar configuración', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        
            return back()->with('error', 'Error al cargar la configuración: ' . $e->getMessage());
        }
    }

    /**
     * Update profile information.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'correo' => 'required|email|max:150',
            'telefono' => 'nullable|string|max:20',
            'doc_identidad' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date'
        ]);

        try {
            // Obtener el usuario autenticado (igual que en DashboardController)
            $user = Auth::user();
            $trabajador = $user->trabajador;
            
            if (!$trabajador) {
                return response()->json(['error' => 'No se encontró información del trabajador'], 404);
            }

            // Verificar si el correo ya existe (excepto el usuario actual)
            $correoExiste = DB::table('usuarios')
                              ->where('correo', $request->correo)
                              ->where('id', '!=', $user->id)
                              ->exists();
            
            if ($correoExiste) {
                return response()->json(['error' => 'El correo electrónico ya está en uso'], 422);
            }

            // Actualizar usuario
            DB::table('usuarios')->where('id', $user->id)->update([
                'correo' => $request->correo
            ]);

            // Actualizar trabajador
            DB::table('trabajadores')->where('id', $trabajador->id)->update([
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'telefono' => $request->telefono,
                'doc_identidad' => $request->doc_identidad,
                'fecha_nacimiento' => $request->fecha_nacimiento
            ]);

            // Obtener el trabajador actualizado para las iniciales
            $trabajadorActualizado = Trabajador::find($trabajador->id);

            Log::info('Perfil actualizado', [
                'usuario_id' => $user->id,
                'trabajador_id' => $trabajador->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado correctamente',
                'data' => [
                    'nombre_completo' => $trabajadorActualizado->nombre_completo,
                    'iniciales' => $trabajadorActualizado->iniciales
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar perfil', [
                'error' => $e->getMessage(),
                'data' => $request->except(['_token'])
            ]);
            
            return response()->json(['error' => 'Error al actualizar el perfil'], 500);
        }
    }

    /**
     * Upload profile photo.
     */
    public function uploadPhoto(Request $request)
    {
        try {
            Log::info('=== INICIO uploadPhoto ===', [
                'usuario_autenticado' => Auth::check(),
                'tiene_archivo' => $request->hasFile('photo'),
                'metodo_request' => $request->method()
            ]);

            // Validación inicial
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120' // 5MB max
            ]);

            Log::info('Validación pasada correctamente');

            // Obtener el usuario autenticado
            $user = Auth::user();
            
            if (!$user) {
                Log::error('Usuario no autenticado en uploadPhoto');
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            Log::info('Usuario autenticado obtenido', [
                'user_id' => $user->id,
                'correo' => $user->correo
            ]);

            $file = $request->file('photo');
            
            if (!$file) {
                Log::error('No se pudo obtener el archivo del request');
                return response()->json(['error' => 'No se pudo procesar el archivo'], 400);
            }

            Log::info('Archivo obtenido del request', [
                'nombre_original' => $file->getClientOriginalName(),
                'tamaño' => $file->getSize(),
                'tipo_mime' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension()
            ]);

            if (!$file->isValid()) {
                Log::error('Archivo no es válido', [
                    'error' => $file->getError()
                ]);
                return response()->json(['error' => 'El archivo no es válido'], 400);
            }

            // Crear directorio si no existe
            $directorio = 'perfiles';
            $directorioCompleto = storage_path('app/public/' . $directorio);
            
            if (!file_exists($directorioCompleto)) {
                Log::info('Creando directorio de perfiles', ['ruta' => $directorioCompleto]);
                if (!mkdir($directorioCompleto, 0755, true)) {
                    Log::error('No se pudo crear el directorio de perfiles', ['ruta' => $directorioCompleto]);
                    return response()->json(['error' => 'Error al crear directorio de almacenamiento'], 500);
                }
            }
            
            // Generar nombre único para la foto
            $extension = strtolower($file->getClientOriginalExtension());
            $nombreArchivo = 'perfil_' . $user->id . '_' . time() . '.' . $extension;
            $rutaRelativa = $directorio . '/' . $nombreArchivo;
            $rutaCompleta = storage_path('app/public/' . $rutaRelativa);
            
            Log::info('Nombre de archivo generado', [
                'nombre_archivo' => $nombreArchivo,
                'ruta_relativa' => $rutaRelativa,
                'ruta_completa' => $rutaCompleta
            ]);

            // Intentar guardar el archivo directamente
            try {
                // Primero intentamos con el método move
                if (!$file->move(storage_path('app/public/' . $directorio), $nombreArchivo)) {
                    // Si falla, intentamos con store
                    $rutaRelativa = $file->store($directorio, 'public');
                    if (!$rutaRelativa) {
                        throw new \Exception('No se pudo guardar el archivo');
                    }
                    $rutaCompleta = storage_path('app/public/' . $rutaRelativa);
                } else {
                    $rutaRelativa = $directorio . '/' . $nombreArchivo;
                }
                
                Log::info('Archivo guardado exitosamente', [
                    'ruta' => $rutaRelativa,
                    'ruta_completa' => $rutaCompleta
                ]);
            } catch (\Exception $e) {
                Log::error('Error al guardar el archivo', [
                    'error' => $e->getMessage(),
                    'ruta_destino' => storage_path('app/public/' . $directorio)
                ]);
                return response()->json(['error' => 'Error al guardar la imagen: ' . $e->getMessage()], 500);
            }

            // Verificar que el archivo existe
            if (!file_exists($rutaCompleta)) {
                Log::error('El archivo no existe después de guardarlo', ['ruta' => $rutaCompleta]);
                return response()->json(['error' => 'Error al verificar la imagen guardada'], 500);
            }

            // Iniciar una transacción de base de datos
            DB::beginTransaction();
            
            try {
                // Eliminar foto anterior si existe
                if ($user->foto) {
                    Log::info('Buscando foto anterior...', ['foto_id' => $user->foto]);
                    
                    $fotoAnterior = Archivo::find($user->foto);
                    
                    if ($fotoAnterior) {
                        Log::info('Foto anterior encontrada', ['id' => $fotoAnterior->id, 'ruta' => $fotoAnterior->ruta]);
                        
                        // Eliminar archivo físico si existe
                        $rutaAnterior = storage_path('app/public/' . $fotoAnterior->ruta);
                        if (file_exists($rutaAnterior)) {
                            try {
                                unlink($rutaAnterior);
                                Log::info('Archivo físico anterior eliminado', ['ruta' => $rutaAnterior]);
                            } catch (\Exception $e) {
                                Log::warning('No se pudo eliminar el archivo físico anterior', [
                                    'ruta' => $rutaAnterior,
                                    'error' => $e->getMessage()
                                ]);
                                // Continuamos aunque no se pueda eliminar el archivo anterior
                            }
                        }
                        
                        // Eliminar registro en base de datos
                        $fotoAnterior->delete();
                        Log::info('Registro anterior eliminado de BD');
                    } else {
                        Log::warning('No se encontró la foto anterior en la BD', ['foto_id' => $user->foto]);
                    }
                }

                // Crear nuevo registro de archivo
                Log::info('Creando registro en tabla archivos...');
                
                $archivo = new Archivo();
                $archivo->nombre = 'Foto de perfil - ' . $user->correo;
                $archivo->ruta = $rutaRelativa;
                $archivo->save();
                
                if (!$archivo->id) {
                    Log::error('No se pudo insertar el archivo en la base de datos');
                    throw new \Exception('Error al registrar la imagen en la base de datos');
                }
                
                Log::info('Archivo registrado en BD', [
                    'archivo_id' => $archivo->id,
                    'ruta' => $rutaRelativa
                ]);

                // Actualizar usuario con nueva foto
                Log::info('Actualizando usuario con nueva foto...');
                
                // Usar update directo en lugar de save para evitar problemas con modelos
                $updated = DB::table('usuarios')->where('id', $user->id)->update([
                    'foto' => $archivo->id
                ]);
                
                if (!$updated) {
                    Log::error('No se pudo actualizar el usuario con la nueva foto');
                    throw new \Exception('Error al actualizar el perfil del usuario');
                }
                
                Log::info('Usuario actualizado exitosamente', [
                    'usuario_id' => $user->id,
                    'archivo_id' => $archivo->id
                ]);
                
                // Si todo está bien, confirmar la transacción
                DB::commit();

                $photoUrl = asset('storage/' . $rutaRelativa);
                
                Log::info('=== FIN uploadPhoto EXITOSO ===', [
                    'photo_url' => $photoUrl
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Foto de perfil actualizada correctamente',
                    'photo_url' => $photoUrl
                ]);
                
            } catch (\Exception $e) {
                // Si algo sale mal, revertir la transacción
                DB::rollBack();
                
                // Intentar eliminar el archivo que acabamos de subir
                if (file_exists($rutaCompleta)) {
                    try {
                        unlink($rutaCompleta);
                        Log::info('Archivo revertido (eliminado) debido a error', ['ruta' => $rutaCompleta]);
                    } catch (\Exception $ex) {
                        Log::warning('No se pudo eliminar el archivo durante rollback', [
                            'ruta' => $rutaCompleta,
                            'error' => $ex->getMessage()
                        ]);
                    }
                }
                
                Log::error('Error en transacción de BD', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json(['error' => 'Error al procesar la imagen: ' . $e->getMessage()], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación en uploadPhoto', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'error' => 'Error de validación: ' . implode(', ', collect($e->errors())->flatten()->toArray())
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('=== ERROR GENERAL EN uploadPhoto ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile(),
                'request_data' => [
                    'method' => $request->method(),
                    'has_file' => $request->hasFile('photo')
                ]
            ]);
            
            return response()->json([
                'error' => 'Error al procesar la imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update security settings (change password).
     */
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()],
            'new_password_confirmation' => 'required|string|same:new_password'
        ]);

        try {
            // Obtener el usuario autenticado (igual que en DashboardController)
            $user = Auth::user();
        
            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            Log::info('Verificando contraseña para cambio', [
                'usuario_id' => $user->id,
                'clave_actual_length' => strlen($user->clave),
                'input_password_length' => strlen($request->current_password)
            ]);

            // Verificar contraseña actual usando Hash::check (para contraseñas encriptadas)
            if (!Hash::check($request->current_password, $user->clave)) {
                Log::warning('Contraseña actual incorrecta', [
                    'usuario_id' => $user->id,
                    'error' => 'Hash::check falló'
                ]);
                return response()->json(['error' => 'La contraseña actual es incorrecta'], 422);
            }

            Log::info('Contraseña actual verificada correctamente', [
                'usuario_id' => $user->id
            ]);

            // Encriptar la nueva contraseña antes de guardarla
            $nuevaClaveEncriptada = Hash::make($request->new_password);

            // Actualizar contraseña en la base de datos
            $updated = DB::table('usuarios')->where('id', $user->id)->update([
                'clave' => $nuevaClaveEncriptada
            ]);

            Log::info('Contraseña actualizada correctamente', [
                'usuario_id' => $user->id,
                'updated' => $updated,
                'nueva_clave_length' => strlen($nuevaClaveEncriptada)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);
        
        } catch (\Exception $e) {
            Log::error('Error al actualizar contraseña', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        
            return response()->json(['error' => 'Error al actualizar la contraseña: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generar iniciales del nombre completo
     */
    private function getIniciales($nombres, $apellidoPaterno)
    {
        $iniciales = '';
        if ($nombres) {
            $iniciales .= strtoupper(substr($nombres, 0, 1));
        }
        if ($apellidoPaterno) {
            $iniciales .= strtoupper(substr($apellidoPaterno, 0, 1));
        }
        return $iniciales ?: 'NN';
    }
}
