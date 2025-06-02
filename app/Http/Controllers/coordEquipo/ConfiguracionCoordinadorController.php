<?php

namespace App\Http\Controllers\coordEquipo;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoordEquipo\ActualizarClaveRequest;
use App\Repositories\EquipoRepositorio;
use App\Repositories\EstadoRepositorio;
use App\Repositories\MetaRepositorio;
use App\Repositories\TareaRepositorio;
use App\Repositories\TrabajadorRepositorio;
use App\Repositories\UsuarioRepositorio;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Password;
use PhpParser\Node\Expr\Cast\Object_;
use Storage;
use User;
use Validator;

class ConfiguracionCoordinadorController extends Controller
{

    protected EstadoRepositorio $estadoRepositorio;
    protected TareaRepositorio $tareaRepositorio;
    protected MetaRepositorio $metaRepositorio;
    protected TrabajadorRepositorio $trabajadorRepositorio;
    protected EquipoRepositorio $equipoRepositorio;
    protected UsuarioRepositorio $usuarioRepositorio;

    public function __construct(EstadoRepositorio $estadoRepositorio, TareaRepositorio $tareaRepositorio, MetaRepositorio $metaRepositorio, TrabajadorRepositorio $trabajadorRepositorio, EquipoRepositorio $equipoRepositorio, UsuarioRepositorio $usuarioRepositorio) {
        $this->estadoRepositorio = $estadoRepositorio;
        $this->tareaRepositorio = $tareaRepositorio;
        $this->metaRepositorio = $metaRepositorio;
        $this->trabajadorRepositorio = $trabajadorRepositorio;
        $this->equipoRepositorio = $equipoRepositorio;
        $this->usuarioRepositorio = $usuarioRepositorio;
    }

    public function index()
    {
        $usuario = Auth::user();
        $trabajador = $this->trabajadorRepositorio->findOneBy('usuario_id', $usuario->id); 
        
        return view('private.coord-equipo.configuracion', [
            'usuario' => $usuario,
            'trabajador' => $trabajador
        ]);
    }
    
    public function actualizarPerfil(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'ubicacion' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Lógica para actualizar el perfil
        // ...
        
        return redirect()->route('coordinador-grupo.configuracion')
            ->with('success', 'Perfil actualizado correctamente');
    }
    
    public function cambiarPassword(ActualizarClaveRequest $request)
    {
        $usuario = Auth::user();

        $this->usuarioRepositorio->update($usuario->id, [
            'clave' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
