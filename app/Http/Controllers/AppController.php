<?php



namespace App\Http\Controllers;



use Inertia\Inertia;

use Inertia\Response;



class AppController {

    public function index(): Response

    {

        return Inertia::render('Index', [

            'title' => 'Laravel Inertia.js, Svelte',

        ]);

    }
}
