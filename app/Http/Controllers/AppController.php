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
        $items_with_category = DB::select(
            'SELECT items.id, categories.name AS category_name
            FROM items
            JOIN items_categories ON items.id = items_categories.item_id
            JOIN categories ON items_categories.category_id = categories.id
            ORDER BY categories.name, items.name ASC'
        );
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
        $items = DB::select(
            'SELECT * FROM items;'
        );
        $itemMap = collect($items)->keyBy('id');

        return $itemMap;
    }

    private function renderCart(User $user): Collection
    {
        $cart_items = DB::select(
            'SELECT item_id, amount FROM cart_items
            WHERE user_id = ?',
            [$user['id']]
        );
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
                'open_cart' => $request->query('open_cart') == true,
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
            'id' => 'required',
            'amount' => 'required|integer',
        ]);

        // validate if id exists in table
        $ids = DB::select(
            'SELECT * FROM items
            WHERE id = ?',
            [$request['id']]
        );

        if (count($ids) == 0) {
            return back()->withInput($request->input())
                ->withErrors(['id' => 'Item doesn\'t exist!']);
        }

        if ($request->user()) {
            if ($validated['amount'] <= 0) {
                DB::statement(
                    'DELETE FROM cart_items
                    WHERE user_id = ? AND item_id = ?',
                    [$request->user()['id'], $validated['id']]
                );
            } else {
                DB::statement(
                    'INSERT INTO cart_items (user_id, item_id, amount)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE amount = ?',
                    [$request->user()['id'], $validated['id'], $validated['amount'],
                        $validated['amount']]
                );
            }
        }

        return to_route('index');
    }
}
