<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $continuation = $request->continuation ? $request->continuation : "/";

        $remember = $request->boolean($request->remember);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect($continuation);
        }

        return back()->withErrors([
            'incorrectCredentials' => true
        ])->withInput($request->only('email', 'remember', 'continuation'));
    }

    public function register(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6'],
        ]);

        $name = $credentials['name'];
        $email = $credentials['email'];
        $password = Hash::make($credentials['password']);
        $time = now();

        DB::statement(
            'INSERT INTO users (name, email, password, email_verified_at, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?)', 
            [$name, $email, $password, $time, $time, $time]
        );

        return redirect("login");
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        return back();
    }

    public function displayLogin(Request $request): Response
    {
        $values = [
            'email' => $request->old('email'),
            'password' => '',
            'remember' => $request->old('remember'),
            'continuation' => parse_url(url()->previous(), PHP_URL_PATH),
        ];

        return Inertia::render('Login', ['values' => $values]);
    }

    public function displaySignup(Request $request): Response
    {
        $values = [
            'name' => $request->old('name'),
            'email' => $request->old('email'),
            'password' => '',
        ];

        return Inertia::render('Signup', ['values' => $values]);
    }
}
