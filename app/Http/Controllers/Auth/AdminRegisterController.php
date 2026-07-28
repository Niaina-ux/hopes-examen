<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminRegisterController extends Controller
{
    //inscription d'in seul admin
    public function admin_register()
    {
        $adminExists = User::where('role', 'admin')->exists();

        if ($adminExists) {
            return redirect()->route('login')
                ->with('error', 'Un compte administrateur existe déjà, il n\'est plus possible d\'en créer un nouveau.');
        }

        return view('auth.register');
    }

    //store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:100',
                'unique:users,email',
                'regex:/^(?!.*\.(\w+)\.\1$).+$/i',
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);

        Auth::login($user);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Compte admin ajouter avec succes.');
    }
}
