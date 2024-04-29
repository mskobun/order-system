<?php

namespace App\Http\Controllers;

use App\AuthUtils;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;

// Handles endpoints related to customer orders
class OrderController
{
    private const TAX = 0.06;

    private const DELIVERY = 5.0;

    private static function addTotalsToOrder(&$order)
    {
        $subtotal = collect($order->items)->reduce(function ($carry, $item) {
            return $carry + $item->price * $item->amount;
        }, 0);

        $order->subtotal = $subtotal;

        $totalAfterDelivery = $subtotal + $order->delivery_fee;

        $tax_amount = $totalAfterDelivery * $order->tax;
        $order->tax_amount = $tax_amount;

        $totalAfterTax = $totalAfterDelivery + $tax_amount;

        $discount_amount = $totalAfterTax * $order->discount;
        $order->discount_amount = $discount_amount;

        $totalAfterPercentDiscount = $totalAfterTax - $discount_amount;
        $totalAfterPriceReduction = $totalAfterPercentDiscount - $order->price_reduction;

        if ($totalAfterPriceReduction < 0) {
            $totalAfterPriceReduction = 0;
        }

        $order->total = $totalAfterPriceReduction;
    }

    public function applyVoucher(string $voucherCode, float &$price_reduction, float &$discount, bool &$invalidVoucher): bool
    {
        $voucher = DB::select(
            'SELECT * FROM promos 
            WHERE code = ?',
            [$voucherCode]
        );

        if (count($voucher) == 0) {
            $invalidVoucher = true;
            return false;
        }

        $time_start = strtotime($voucher[0]->begins_at);
        $time_end = strtotime($voucher[0]->ends_at);
        $cur_time = time();

        if (!($cur_time > $time_start && $cur_time < $time_end)) {
            $invalidVoucher = true;
            return false;
        }
 
        $price_reduction += $voucher[0]->price_reduction;
        $discount += $voucher[0]->discount;
        return true;
    }

    // Renders the order confirmation page
    public function confirmOrder(Request $request): Response
    {
        $items = DB::select(
            'SELECT cart_items.amount, items.*
            FROM cart_items
            JOIN items
            ON cart_items.item_id = items.id
            WHERE user_id = ?
            ORDER BY cart_items.amount DESC, items.name;',
            [AuthUtils::getUser($request)->id]
        );

        $order = (object) [
            'items' => $items,
        ];

        Log::debug($request->dineIn);

        $deliveryValue = false ? 0 : self::DELIVERY;

        Log::debug($deliveryValue);

        $order->delivery_fee = $deliveryValue;

        $order->tax = OrderController::TAX;

        $order->price_reduction = 0;
        $order->discount = 0;
        
        $invalidVoucher = false;
        $voucherApplied = false;
        if ($request->voucher) {
            $voucherApplied = OrderController::applyVoucher($request->voucher, $order->price_reduction, $order->discount, $invalidVoucher);
        }

        OrderController::addTotalsToOrder($order);

        return Inertia::render('ConfirmOrderAndPay', [
            'detailsUpdated' => $request->old('updatedProfile'),
            'orderDetails' => $order,
            'voucherApplied' => $voucherApplied, 
            'invalidVoucher' => $invalidVoucher,
        ]);
    }

    // Renders the order list page
    public function listOrders(Request $request): Response
    {
        // Latest order first
        $orders = collect(
            DB::select(
                'SELECT * FROM orders
                WHERE user_id = ?
                ORDER BY id DESC',
                [AuthUtils::getUser($request)->id]
            )
        );
        $orders = $orders->map(function ($order, $key) {
            $statuses = DB::select(
                'SELECT * FROM order_status
                ORDER BY created_at DESC, status DESC',
                []
            );
            $items = DB::select(
                'SELECT order_items.*
                FROM order_items
                WHERE order_id = ?
                ORDER BY amount DESC, name',
                [$order->id]
            );
            $order->items = $items;
            $order->statuses = $statuses;

            // add subtotal/total to order
            OrderController::addTotalsToOrder($order);

            return $order;
        });

        return Inertia::render('Orders', [
            'orders' => $orders,
        ]);
    }

    // Copied from https://www.regular-expressions.info/creditcard.html
    const CC_REGEX = "/^(?:4[0-9]{12}(?:[0-9]{3})?|(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|6(?:011|5[0-9]{2})[0-9]{12}|(?:2131|1800|35\d{3})\d{11})$/";

    public function submitOrder(Request $request): RedirectResponse
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
            'payment.cvv' => 'required|integer|min:0|max:999',
        ]);

        // Remove payment details as soon as possible.
        // If this was a real-world application, we would send these
        // to the payment processor, but here we just assume the payment
        // went through if the card number is valid.
        unset($validated['payment']);

        // Validating items in the cart
        // Check that the user has at least one item in the cart
        $item_count = DB::scalar(
            'SELECT COUNT(*) FROM cart_items WHERE user_id = ?',
            [AuthUtils::getUser($request)->id]
        );
        if ($item_count < 1) {
            return back()
                ->withInput($request->input())
                ->withErrors(['items' => 'There are no items in the cart']);
        }

        // Check that all items in the cart are available
        $unavailable_items = DB::select(
            'SELECT items.name FROM cart_items
            JOIN items ON cart_items.item_id = items.id
            WHERE cart_items.user_id = ?
            AND items.available = false',
            [AuthUtils::getUser($request)->id]
        );
        if (count($unavailable_items) > 0) {
            $joined_items = collect($unavailable_items)
                ->pluck('name')
                ->join(',');
            $error = 'Some items in your cart are no longer available: '.$joined_items.'. Please remove these items to proceed.';

            return back()
                ->withInput($request->input())
                ->withErrors(['items' => $error]);
        }

        // sets the address to null if it doesn't exist
        if (! array_key_exists('address', $validated)) {
            $validated['address'] = null;
        }

        $orderType = ($validated['dineIn']) ? 'DINE_IN' : 'DELIVERY';

        // calculate delivery fee
        // temporary
        $delivery = 5;

        $dineIn = false;
        if (!is_null($request->dineIn)) {
            $dineIn = $request->dineIn;
        }

        if ($dineIn) {
            $delivery = 0;
        } else {
            $delivery = OrderController::DELIVERY;
        }

        // fetch current tax
        // at the moment assume 6%
        $tax = OrderController::TAX;

        // calculating the discount percentage and price reduction
        // check if the entered promo code is valid or if there is an ongoing discount
        $price_reduction = 0;
        $discount = 0;

        $invalidVoucher = false;
        if ($request->voucher) {
            OrderController::applyVoucher($request->voucher, $price_reduction, $discount, $invalidVoucher);
        }

        // Wrap in a transaction, so that if something fails during the insertion,
        // noting will be inserted, instead of having dangling data
        DB::statement('START TRANSACTION;');
        try {
            $cart_items = DB::select(
                'SELECT items.id, items.name, items.price, cart_items.amount
                FROM cart_items
                JOIN items
                ON items.id = cart_items.item_id
                WHERE user_id = ?',
                [AuthUtils::getUser($request)->id]
            );

            // Create the order first, and get the id
            DB::statement(
                'INSERT INTO orders
                (user_id, type, address, phone, name, delivery_fee, tax, price_reduction, discount)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);',
                [
                    AuthUtils::getUser($request)->id, $orderType, $validated['address'],
                    $validated['phone'], $validated['name'],
                    $delivery, $tax, $price_reduction, $discount,
                ]
            );

            $order_id = DB::scalar('SELECT LAST_INSERT_ID()');

            // Then, insert the order items
            foreach ($cart_items as $item) {
                DB::statement(
                    'INSERT INTO order_items (order_id, item_id, amount, price, name)
                    VALUES (?, ?, ?, ?, ?)',
                    [$order_id, $item->id, $item->amount, $item->price, $item->name]
                );
            }

            // Clear the cart
            DB::statement(
                'DELETE FROM cart_items WHERE user_id = ?',
                [AuthUtils::getUser($request)->id]
            );

            // Create a new status entry.
            // Since we don't actually process payments, create both
            // PAYMENT_PENDING and ACCEPTED statuses at the same time.
            DB::statement(
                'INSERT INTO order_status (order_id, created_at, status)
                VALUES (?, ?, ?)',
                [$order_id, Carbon::now(), 'PAYMENT_PENDING']
            );

            DB::statement(
                'INSERT INTO order_status (order_id, created_at, status)
                VALUES (?, ?, ?)',
                [$order_id, Carbon::now(), 'ACCEPTED']
            );

            DB::statement('COMMIT;');

            return to_route('list_orders');
        } catch (Exception $e) {
            DB::statement('ROLLBACK;');
            throw $e;
        }
    }

    public function orderStatus(Request $request): Response
    {
        return Inertia::render('OrderStatus', ['order_id' => $request->order_id]);
    }
}
