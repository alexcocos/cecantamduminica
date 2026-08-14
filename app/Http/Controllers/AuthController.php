<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Afișează pagina de autentificare.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Autentifică utilizatorul.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ]);

        if (Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();

            return redirect()
                ->intended(route('songs.index'))
                ->with(
                    'success',
                    'Te-ai autentificat cu succes.'
                );
        }

        return back()
            ->withErrors([
                'email' =>
                    'Emailul sau parola sunt incorecte.',
            ])
            ->onlyInput('email');
    }

    /**
     * Afișează pagina de creare a contului.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Creează un utilizator nou.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $user = new User();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make(
            $validated['password']
        );
        $user->role = 'user';

        $user->save();

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('songs.index')
            ->with(
                'success',
                'Contul tău a fost creat.'
            );
    }

    /**
     * Deloghează utilizatorul.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('songs.index')
            ->with(
                'success',
                'Te-ai delogat.'
            );
    }
}