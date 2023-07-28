<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseFormatter;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{

    public function add(Request $request)
    {
        $validate = $request->validate([
            'cart_id' => 'required',
            'product_id' => 'required',
            'total' => 'required'
        ]);

        $validate['user_id'] = $request->user()->id;

        try {
            $cartItem = CartItem::create($validate);
            return ResponseFormatter::success(['cart_item' => $cartItem], 'Successs add product to cart');
        } catch (\Exception $err) {
            return ResponseFormatter::error(
                'Failed add product to cart',
                400,
                [
                    'message' => 'Something went wrong',
                    'error' => $err->getMessage()
                ]
            );
        }
    }


    public function get(Request $request)
    {
        $carts = Cart::with(['cartItems.product'])->where('user_id', $request->user()->id)->first();
        return ResponseFormatter::success($carts->cartItems, 'Success get list of cart items');
    }
}
