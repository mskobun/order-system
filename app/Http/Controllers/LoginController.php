<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers;
use App\Models\User;
use PhpParser\Node\Expr\Cast\Bool_;
use Illuminate\Support\Facades\Log;

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

        $continuation = $request->continuation ?? "/";

        $remember = $request->boolean($request->remember);

        if (LoginController::attemptLogin($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect($continuation);
        }

        return back()->withErrors([
            'incorrectCredentials' => true
        ])->withInput($request->only('email', 'remember', 'continuation'));
    }

    public function attemptLogin(array $credentials, bool $remember): bool
    {
        $email = $credentials['email'];
        $password = $credentials['password'];

        // retrieving the user from the database
        $users = DB::select(
            'SELECT * FROM users
            WHERE email = ?',
            [$email]
        );

        if (count($users) == 0) {
            return false;
        }

        $user = $users[0];

        // checking credentials
        $success = Hash::check($password, $user->password);
        
        if ($success) {
            $model_user = new User;
            $model_user->id = $user->id;
            $model_user->name = $user->name;
            $model_user->email = $user->email;
            $model_user->email_verified_at = $user->email_verified_at;
            $model_user->password = $user->password;
            $model_user->remember_token = $user->remember_token;
            $model_user->created_at = $user->created_at;
            $model_user->updated_at = $user->updated_at;

            Auth::login($model_user, $remember);
            
            return true;
        }

        return false;
    }

    public function register(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'name' => 'required',
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        // ensuring the email is unique manually since the rule for it accesses the DB with an ORM
        $emails = DB::select(
            'SELECT * FROM users
            WHERE email = ?',
            [$credentials['email']]
        );
        
        if (count($emails) !== 0) {
            return back()->withErrors(['email' => 'This email already exists.']);
        }

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

        return Inertia::render('Signup', ['values' => $values, 'password_confirmation' => '']);
    }
}
