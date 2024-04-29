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

// Handles endpoints related to authentication
class LoginController
{
    // Handles an authentication attempt
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

    // Attempts to login with a set of credentials
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

    private function isValidPassword(string $password, string $confirmation, array &$errors): bool
    {
        $hadError = false;

        // Checks if the password satisfies a certain format
        // Specifically, it must contain 3 of the following 5 categories
        // - Uppercase characters
        // - Lowercase characters
        // - Digits
        // - Special characters
        // - Unicode characters
        if (preg_match('/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/', $password) !== 1) {
            $errors = array_merge($errors, ['passwordRegexError' => 'The password didn\'t fit the specified format']);
            $hadError = true;
        }

        // Minimum length check
        $min_length = 6;
        if (strlen($password) < $min_length) {
            $errors = array_merge($errors, ['passwordLengthError' => "The password must be at least $min_length characters long"]);
            $hadError = true;
        }

        // Confirmation
        if ($password !== $confirmation) {
            $errors = array_merge($errors, ['passwordLengthError' => 'The password confirmation did not match']);
            $hadError = true;
        }

        return !$hadError;
    }

    // Registers a new user account
    public function register(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'name' => 'required',
            'email' => ['required', 'email'],
            'newPassword' => ['required'],
        ]);

        // Fills up with errors from isValidPassword
        $errors = [];

        if (!LoginController::isValidPassword($request->newPassword, $request->passwordConfirmation, $errors)) {
            return back()->withErrors($errors)->withInput($request->input());
        }

        // Ensuring the email is unique manually (instead of using the validate() rule) since the rule for it accesses the DB with an ORM
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

        // Preparing to register a new account
        // Use of htmlentities to prevent XSS
        $name = htmlentities($credentials['name']);
        $email = htmlentities($credentials['email']);
        $password = Hash::make($credentials['newPassword']);
        $time = now();

        DB::statement(
            'INSERT INTO users (name, email, password, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?)',
            [$name, $email, $password, $time, $time]
        );

        return redirect('login');
    }

    // Updates user information
    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required'],
            'phone' => ['required'],
            'address' => ['required'],
        ]);

        $user = AuthUtils::getUser($request);

        // htmlentities to prevent XSS
        $name = htmlentities($request['name']);
        $email = htmlentities($request['email']);
        $phone = htmlentities($request['phone']);
        $address = htmlentities($request['address']);
        $id = $user->id;

        if (!is_null($user)) {
            DB::statement(
                'UPDATE users
                SET name = ?,
                    email = ?,
                    phone = ?,
                    address = ?
                WHERE id = ?',
                [
                    $name, $email, $phone, $address,
                    $id
                ]
            );
        }

        return back()->withInput(
            ['updatedProfile' => true]
        );
    }

    // Updates user password
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'oldPassword' => ['required'],
            'newPassword' => ['required'],
            'passwordConfirmation' => ['required'],
        ]);

        $user = AuthUtils::getUser($request);

        // Is the old password correct?
        $correct = Hash::check($request->oldPassword, $user->password) && !is_null($user);

        if (!$correct) {
            return back()->withErrors(['passwordWrongError' => 'Incorrect password!'])->withInput($request->input());
        }

        // Checks if the new password is a valid one
        $errors = [];
        if (!LoginController::isValidPassword($request->newPassword, $request->passwordConfirmation, $errors)) {
            return back()->withErrors($errors)->withInput($request->input());
        }

        // Making the new password hash and adding it to DB
        $newPasswordHash = Hash::make($request->newPassword);

        DB::statement(
            'UPDATE users
            SET password = ?
            WHERE id = ?',
            [
                $newPasswordHash,
                $user->id
            ]
        );

        return back()->withInput(['updatedPassword' => true]);
    }

    public function logout(Request $request): RedirectResponse
    {
        AuthUtils::logout();
        AuthUtils::invalidateSession($request);

        return redirect('/');
    }

    public function displayProfile(Request $request): Response
    {
        $user = AuthUtils::getUser($request);

        return Inertia::render('Profile', [
            'user' => $user,
            'updatedProfile' => $request->old('updatedProfile'),
            'updatedPassword' => $request->old('updatedPassword'),
        ]);
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

        return Inertia::render('Signup', [
            'values' => $values,
            'passwords' => $passwords
        ]);
    }
}
