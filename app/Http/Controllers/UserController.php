<?php
namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserController {

    public function list(): Response

    {
        $users = User::query()->select(["name", "email"])->get();
        return Inertia::render('Users', ['users' => $users]);
    }
}
