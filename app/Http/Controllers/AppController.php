<?php
namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Inertia\Response;

class AppController {

    public function index(): Response
    {
        $items = DB::select("select items.*, categories.name as category_name from items join items_categories on items.id = items_categories.item_id join categories on items_categories.category_id = categories.id");
        $response =
            collect($items)
                ->groupBy('category_name')
                ->map(function ($items, $category_name) {
                    $items = $items->select(["id", "name", "description", "image_url", "available", "price"]);
                    return array("name" => $category_name, "items" => $items);})
                ->values();

        return Inertia::render('Index', [
            'menu' => $response,
        ]);
    }
}
