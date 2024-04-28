<?php

namespace App\Http\Controllers;

use App\AuthUtils;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
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

        $remember = $request->boolean($request->remember);

        // passed into attemptLogin, and fills up with the errors generated
        $errors = [];

        if (LoginController::attemptLogin($credentials, $remember, $errors)) {
            AuthUtils::regenerateSession($request);

            return redirect('/');
        }

        return back()->withErrors(
            $errors
        )->withInput($request->only('email', 'remember'));
    }

    public function attemptLogin(array $credentials, bool $remember, array &$errors): bool
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
            $model_user->password = $user->password;
            $model_user->created_at = $user->created_at;
            $model_user->updated_at = $user->updated_at;

            AuthUtils::login($model_user, $remember);

            return true;
        }

        $errors = array_merge($errors, ['passwordError' => true]);

        return false;
    }

    public function isValidPassword(string $password, string $confirmation, array &$errors): bool
    {
        $hadError = false;

        if (preg_match('/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/', $password) !== 1) {
            $errors = array_merge($errors, ['passwordRegexError' => 'The password didn\'t fit the specified format']);
            $hadError = true;
        }

        $min_length = 6;
        if (strlen($password) < $min_length) {
            $errors = array_merge($errors, ['passwordLengthError' => "The password must be at least $min_length characters long"]);
            $hadError = true;
        }

        if ($password !== $confirmation) {
            $errors = array_merge($errors, ['passwordLengthError' => 'The password confirmation did not match']);
            $hadError = true;
        }

        return !$hadError;
    }

    public function register(Request $request): RedirectResponse
    {
        // automatically generates a redirect with errors corresponding to the keys here,
        // like 'password' => The password field must be at least 6 characters
        $credentials = $request->validate([
            'name' => 'required',
            'email' => ['required', 'email'],
            'newPassword' => ['required'],
        ]);

        $errors = [];
        if (!LoginController::isValidPassword($request->newPassword, $request->passwordConfirmation, $errors)) {
            return back()->withErrors($errors)->withInput($request->input());
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
                $request->only('name', 'email', 'newPassword', 'passwordConfirmation')
            );
        }

        $name = $credentials['name'];
        $email = $credentials['email'];
        $password = Hash::make($credentials['newPassword']);
        $time = now();

        DB::statement(
            'INSERT INTO users (name, email, password, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?)',
            [$name, $email, $password, $time, $time]
        );

        return redirect('login');
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

        if (! is_null($user)) {
            DB::statement(
                'UPDATE users
                SET name = ?,
                    email = ?,
                    phone = ?,
                    address = ?
                WHERE id = ?',
                [$request['name'], $request['email'], $request['phone'], $request['address'],
                    $user->id]
            );
        }

        return back()->withInput(
            ['updated' => true]
        );
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'oldPassword' => ['required'],
            'newPassword' => ['required'],
            'passwordConfirmation' => ['required'],
        ]);

        $user = AuthUtils::getUser($request);

        $correct = Hash::check($request->oldPassword, $user->password) && !is_null($user); 

        if (!$correct) {
            return back()->withErrors(['passwordWrongError' => 'Incorrect password!'])->withInput($request->input());
        }

        $errors = [];
        if (!LoginController::isValidPassword($request->newPassword, $request->passwordConfirmation, $errors)) {
            return back()->withErrors($errors)->withInput($request->input());
        }

        $newPasswordHash = Hash::make($request->newPassword);

        DB::statement(
            'UPDATE users
            SET password = ?
            WHERE id = ?',
            [$newPasswordHash,
                $user->id]
        );

        return back()->withInput(['updatedPassword' => true]);
    }

    public function logout(Request $request): RedirectResponse
    {
        AuthUtils::logout();
        AuthUtils::invalidateSession($request);

        return redirect('/');
    }

    public function displayLogin(Request $request): Response
    {
        $values = [
            'email' => $request->old('email'),
            'password' => '',
            'remember' => $request->old('remember'),
        ];

        return Inertia::render('Login', ['values' => $values]);
    }

    public function displaySignup(Request $request): Response
    {
        $values = [
            'name' => $request->old('name'),
            'email' => $request->old('email'),
        ];
        
        $passwords = [
            'newPassword' => $request->old('newPassword'),
            'passwordConfirmation' => $request->old('passwordConfirmation'),
        ];

        return Inertia::render('Signup', ['values' => $values, 'passwords' => $passwords]);
    }
}
