<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegistroRequest;
use App\Repositories\EmpresaRepositorio;
use App\Repositories\PlanRepositorio;
use App\Repositories\UsuarioRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{

    protected PlanRepositorio $planRepositorio;
    protected UsuarioRepositorio $usuarioRepositorio;
    protected EmpresaRepositorio $empresaRepositorio;

    public function __construct(PlanRepositorio $planRepositorio, UsuarioRepositorio $usuarioRepositorio, EmpresaRepositorio $empresaRepositorio)
    {
        $this->planRepositorio = $planRepositorio;
        $this->usuarioRepositorio = $usuarioRepositorio;
        $this->empresaRepositorio = $empresaRepositorio;
    }

    public function index()
    {

        return view('public.auth.login');
    }
    public function showLoginForm()
    {
        return view('public.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = [
            'correo' => $request->email,
            'password' => $request->password,
        ];

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Actualizar última conexión
            $usuario = Auth::user();
            $usuario->ultima_conexion = now();
            $usuario->en_linea = true;
            $usuario->save();

            return $this->redirectByRole($usuario);
        }

        return back()->withErrors([

            'password' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->withInput($request->only('email', 'remember'));
    }

    public function showRegisterForm()
    {
        $planes = $this->planRepositorio->getAll();
        return view('public.auth.register', ['planes' => $planes]);
    }

    public function register(RegistroRequest $request)
    {
        DB::beginTransaction();

        try {
            $correoGenerado = $request->email . '@collaborax.com';

            if ($this->usuarioRepositorio->existeCorreo($correoGenerado)) {
                return back()->withErrors(['email' => 'El correo ya está registrado.'])->withInput();
            }

            $usuario = $this->usuarioRepositorio->create([
                'correo' => $correoGenerado,
                'correo_personal' => $request->email_personal,
                'clave' => bcrypt($request->password),
                'clave_mostrar' => $request->password,
                'rol_id' => 2, // ROL DE EMPRESA
                'activo' => true,
                'en_linea' => false,
                'ultima_conexion' => now(),
                'foto' => null,
            ]);

            $this->empresaRepositorio->create([
                'usuario_id' => $usuario->id,
                'plan_servicio_id' =>(int) $request->plan,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'ruc' => $request->ruc,
                'telefono' => $request->telefono,
            ]);

            DB::commit();

            return redirect()->route('login')->with('success', 'Cuenta registrada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en registro: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Ocurrió un error durante el registro.'])->withInput();
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $usuario = Auth::user();
            $usuario->en_linea = false;
            $usuario->ultima_conexion = now();
            $usuario->save();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Sesión cerrada correctamente.');
    }

    private function redirectByRole($user)
    {
        switch ($user->rol->nombre) {
            case 'Super Admin':
                return redirect()->route('super-admin.dashboard');
            case 'Admin':
                return redirect()->route('admin.dashboard.index');
            case 'Coord. General':
                return redirect()->route('coordinador-general.dashboard');
            case 'Coord. Equipo':
                return redirect()->route('coord-equipo.dashboard');
            case 'Colaborador':
                return redirect()->route('colaborador.actividades');
            default:
                return redirect()->route('home');
        }
    }
}