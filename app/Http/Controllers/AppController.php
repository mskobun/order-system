<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AppController
{
    private function renderMenu(): Collection
    {
        $items_with_category = DB::select('select items.id, categories.name as category_name from items join items_categories on items.id = items_categories.item_id join categories on items_categories.category_id = categories.id order by categories.name, items.name asc');
        $menu =
            collect($items_with_category)
                ->groupBy('category_name')
                ->map(function ($items, $category_name) {
                    $items = $items->map(function ($item, $key) {
                        return $item->id;
                    });

                    return ['name' => $category_name, 'items' => $items];
                })
                ->values();

        return $menu;
    }

    private function renderItems(): Collection
    {
        $items = DB::select('select * from items;');
        $itemMap = collect($items)->keyBy('id');

        return $itemMap;
    }

    private function renderCart(User $user): Collection
    {
        $cart_items = DB::select('select item_id, amount from cart_items where user_id = ?', [$user['id']]);
        $renderedCart = collect($cart_items)
            ->keyBy('item_id')
            ->map(function ($item, $key) {
                return $item->amount;
            });

        return $renderedCart;

    }

    public function index(Request $request): Response
    {
        if ($request->user()) {
            return Inertia::render('Index', [
                'menu' => self::renderMenu(),
                'items' => self::renderItems(),
                'cart' => self::renderCart($request->user()),
            ]);
        } else {
            return Inertia::render('Index', [
                'menu' => self::renderMenu(),
                'items' => self::renderItems(),
            ]);
        }
    }

    public function modify_cart(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => 'required|exists:items,id',
            'amount' => 'required|integer',
        ]);
        if ($request->user()) {
            if ($validated['amount'] <= 0) {
                DB::statement('delete from cart_items where user_id = ? and item_id = ?', [$request->user()['id'], $validated['id']]);
            } else {
                DB::statement('insert into cart_items (user_id, item_id, amount) values (?, ?, ?) on duplicate key update amount = ?', [$request->user()['id'], $validated['id'], $validated['amount'], $validated['amount']]);
            }
        }

        return to_route('index');
    }
}
