<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Redirigir según el rol del usuario
            $user = Auth::user();
            return $this->redirectByRole($user);
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company' => 'required|string|max:255',
            'role' => 'required|in:colaborador,coordinador-grupo,coordinador-general,admin,super-admin',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company' => $request->company,
            'role' => $request->role,
        ]);

        Auth::login($user);

        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function redirectByRole($user)
    {
        switch ($user->role) {
            case 'super-admin':
                return redirect()->route('super-admin.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'coordinador-general':
                return redirect()->route('coordinador-general.dashboard');
            case 'coordinador-grupo':
                return redirect()->route('coordinador-grupo.dashboard');
            case 'colaborador':
                return redirect()->route('colaborador.dashboard');
            default:
                return redirect()->route('home');
        }
    }
}