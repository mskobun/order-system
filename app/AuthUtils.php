<?php

namespace OrderSystem;

use Illuminate\Support\Facades\Auth;

class AuthUtils
{
    // $user is probably just gonna be a select object in the future
    // atm it has to be a full model user created from the data there
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
    public static function getUser($request)
    {
        return $request->user();
    }

    public static function check($request)
    {
        $user = AuthUtils::getUser($request);
        return !is_null($user);
    }
}