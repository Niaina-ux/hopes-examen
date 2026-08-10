<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class loginController extends Controller
{
    public function login()
    {
        $adminExiste = User::where('role', 'admin')->exists();

        if (Auth::check()) {
            return match (Auth::user()->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'prof' => redirect()->route('prof.dashboard'),
                'student' => redirect()->route('home'),
            };
        }
        return view('/auth/login', compact('adminExiste'));
    }


    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'Les identifiants ne correspondent à aucun compte.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return match ($request->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'prof' => redirect()->route('prof.dashboard'),
            'student' => redirect()->route('home'),
        };
    }


    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
