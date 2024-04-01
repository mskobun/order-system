<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AppController
{
    public function index(): Response
    {
        $items = DB::select('select items.*, categories.name as category_name from items join items_categories on items.id = items_categories.item_id join categories on items_categories.category_id = categories.id');
        $response =
            collect($items)
                ->groupBy('category_name')
                ->map(function ($items, $category_name) {
                    $items = $items->select(['id', 'name', 'description', 'image_url', 'available', 'price']);

                    return ['name' => $category_name, 'items' => $items];
                })
                ->values();

        return Inertia::render('Index', [
            'menu' => $response,
        ]);
    }
}
