<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers;

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

        // just figured out that checkboxes don't send anything if not checked...
        // anyway this handles it
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect($continuation);
        }

        return back()->withErrors([
            'incorrectCredentials' => true
        ])->withInput($request->only('email', 'remember', 'continuation'));
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
}
