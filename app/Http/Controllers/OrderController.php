<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class OrderController
{
    public function confirm_order(Request $request): Response
    {
        $items = DB::select(
            'SELECT cart_items.amount, items.*
            FROM cart_items
            JOIN items
            ON cart_items.item_id = items.id
            WHERE user_id = ?;', [$request->user()->id]);

        return Inertia::render('ConfirmOrderAndPay', [
            'order_items' => $items,
        ]);
    }

    // Copied from https://www.regular-expressions.info/creditcard.html
    const CC_REGEX = "/^(?:4[0-9]{12}(?:[0-9]{3})?|(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|6(?:011|5[0-9]{2})[0-9]{12}|(?:2131|1800|35\d{3})\d{11})$/";

    public function submit_order(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:1',
            'phone' => 'required|string|min:1',
            'dineIn' => 'required|boolean',
            'address' => 'required_if:dineIn,false|string|min:1',
            'payment.type' => ['required', Rule::in(['card'])],
            'payment.number' => ['required', 'regex:'.self::CC_REGEX],
            'payment.expiryMonth' => 'required|integer|min:1|max:12',
            // TODO: better year validation
            'payment.expiryYear' => 'required|integer|min:0',
            'payment.cvv' => 'required|integer|min:0|max:999']);

        // Remove payment details as soon as possible.
        // If this was a real-world application, we would send these
        // to the payment processor, but here we just assume the payment
        // went through if the card number is valid.
        unset($validated['payment']);

        // Prevent XSS by ensuring the HTML tags in user-provided
        // text fields will not be treated as such
        $validated['name'] = htmlentities($validated['name']);
        $validated['phone'] = htmlentities($validated['phone']);

        if (array_key_exists('address', $validated)) {
            $validated['address'] = htmlentities($validated['address']);
        } else {
            $validated['address'] = null;
        }

        $orderType = ($validated['dineIn']) ? 'DINE_IN' : 'DELIVERY';

        // Wrap in a transaction, so that if something fails during the insertion,
        // noting will be inserted, instead of having dangling data
        DB::statement('START TRANSACTION;');
        try {
            // Create the order first, and get the id
            DB::statement(
                'INSERT INTO orders
                (user_id, type, address, phone, name)
                VALUES (?, ?, ?, ?, ?);',
        [$request->user()['id'], $orderType, $validated['address'],
            $validated['phone'], $validated['name']]
            );

            $order_id = DB::scalar('SELECT LAST_INSERT_ID()');

            // Then, add cart items to order items
            $cart_items = DB::select('
            SELECT item_id, amount
            FROM cart_items
            WHERE user_id = ?',
                [$request->user()['id']]
            );

            foreach ($cart_items as $item) {
                DB::statement(
                    'INSERT INTO order_items (order_id, item_id, amount)
                    VALUES (?, ?, ?)', [$order_id, $item->item_id, $item->amount]);
            }

            // Clear the cart
            DB::statement('DELETE FROM cart_items WHERE user_id = ?',
                [$request->user()['id']]);

            // Create a new status entry.
            // Since we don't actually process payments, create both
            // PAYMENT_PENDING and ACCEPTED statuses at the same time.
            DB::statement(
                'INSERT INTO order_status (order_id, created_at, status)
                VALUES (?, ?, ?)',
                [$order_id, Carbon::now(), 'PAYMENT_PENDING']);

            DB::statement(
                'INSERT INTO order_status (order_id, created_at, status)
                VALUES (?, ?, ?)',
                [$order_id, Carbon::now(), 'ACCEPTED']);

            DB::statement('COMMIT;');

            return to_route('order_status', ['order_id' => $order_id]);

        } catch (Exception $e) {
            DB::statement("ROLLBACK;");
            throw $e;
        }
    }

    public function order_status(Request $request): Response
    {
        return Inertia::render('OrderStatus', ['order_id' => $request->order_id]);
    }
}
