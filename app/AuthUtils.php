<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// A class containing all of the authentication-related functions required for the program
// We initially intended to manually implement every function here, to avoid implicit DB calls by Laravel
// But with SESSION_DRIVER=file, only retrieving the user ($request->user()) and Auth::check() used DB calls
// Hence the rest are simply redirects to existing Laravel functions
class AuthUtils
{
    public static function login($user, $remember): void
    {
        Auth::login($user, $remember);
    }

    public static function logout()
    {
        Auth::logout();
    }

    public static function regenerateSession($request)
    {
        $request->session()->regenerate();
    }

    public static function invalidateSession($request)
    {
        $request->session()->invalidate();
    }

    // should return null if user is invalid
    public static function getUser(Request $request)
    {
        // Since implicit DB queries are not allowed,
        // we can't use Auth::user() or $request->user()
        // So instead we manually fetch user id from session and
        // query it it
        $user_id = $request->session()->get(Auth::getFacadeRoot()->guard()->getName());
        if ($user_id) {
            $res = DB::select(
                'SELECT * FROM users 
                WHERE id = ?',
                [$user_id]
            );

            return $res[0];
        } else {
            return null;
        }
    }

    public static function check($request)
    {
        $user = AuthUtils::getUser($request);

        return !is_null($user);
    }
}
