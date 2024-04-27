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
use App\AuthUtils;

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

        // passed into attemptLogin, and fills up with the errors generated
        $errors = [];

        if (LoginController::attemptLogin($credentials, $remember, $errors)) {
            AuthUtils::regenerateSession($request);

            return redirect($continuation);
        }

        return back()->withErrors(
            $errors
        )->withInput($request->only('email', 'remember', 'continuation'));
    }

    public function attemptLogin(array $credentials, bool $remember, array & $errors): bool
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
            $errors = array_merge($errors, ['emailError' => true]);
            return false;
        }

        $user = $users[0];

        // checking credentials
        $success = Hash::check($password, $user->password);
        
        if ($success) {
            // making an instance of the user model manually with data from the SQL statement, so I can use the login function
            $model_user = new User;
            $model_user->id = $user->id;
            $model_user->name = $user->name;
            $model_user->email = $user->email;
            $model_user->email_verified_at = $user->email_verified_at;
            $model_user->password = $user->password;
            $model_user->remember_token = $user->remember_token;
            $model_user->created_at = $user->created_at;
            $model_user->updated_at = $user->updated_at;

            AuthUtils::login($model_user, $remember);
            
            return true;
        }

        $errors = array_merge($errors, ['passwordError' => true]);
        return false;
    }

    public function register(Request $request): RedirectResponse
    {
        // automatically generates a redirect with errors corresponding to the keys here, 
        // like 'password' => The password field must be at least 6 characters
        $credentials = $request->validate([
            'name' => 'required',
            'email' => ['required', 'email'],
            'password' => [
                'required', 
                'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
            ],
        ]);

        $min_length = 6;
        if (strlen($request->password) < $min_length) {
            return back()->withErrors(
                ['passwordLengthError' => "The password must be at least $min_length characters long"]
            )->withInput($request->input());
        }

        if ($request->password !== $request->password_confirmation) {
            return back()->withErrors(
                ['passwordMatchError' => 'The passwords do not match!']
            )->withInput($request->input());
        }

        // ensuring the email is unique manually since the rule for it accesses the DB with an ORM
        $emails = DB::select(
            'SELECT * FROM users
            WHERE email = ?',
            [$credentials['email']]
        );
        
        if (count($emails) !== 0) {
            return back()->withErrors(
                ['email' => 'This email already exists.']
            )->withInput(
                $request->only('name', 'email', 'password', 'password_confirmation')
            );
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

    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required'],
            'phone' => ['required'],
            'address' => ['required'],
        ]);

        $user = AuthUtils::getUser($request);

        if (!is_null($user)) {
            DB::statement(
                'UPDATE users
                SET name = ?,
                    email = ?,
                    phone = ?,
                    address = ?
                WHERE id = ?',
                [$request['name'], $request['email'], $request['phone'], $request['address'],
                $user['id']]
            );
        }

        return to_route('profile')->withInput(
            ['updated' => true]
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        AuthUtils::logout();
        AuthUtils::invalidateSession($request);
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
            'password' => $request->old('password'),
            'password_confirmation' => $request->old('password_confirmation'),
        ];

        return Inertia::render('Signup', ['values' => $values]);
    }
}
