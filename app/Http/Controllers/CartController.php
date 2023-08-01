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
            $tryCart = CartItem::where('product_id', $validate['product_id'])->where('user_id', $validate['user_id'])->first();
            if ($tryCart) {
                $tryCart->update(['total' => $tryCart->total + 1]);
                return ResponseFormatter::success(['cart_item' => $tryCart], 'Successs add product to cart');
            }
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
        $carts = Cart::with(['cartItems.product.galleries'])->where('user_id', $request->user()->id)->first();
        return ResponseFormatter::success($carts->cartItems, 'Success get list of cart items');
    }

    public function delete(Request $request)
    {
        $cart = CartItem::find($request->id);
        $cart->delete();
        return ResponseFormatter::success($cart, 'Success remove cart items');
    }

    public function changeTotal(Request $request)
    {
        $validate = $request->validate([
            'cart_id' => 'required',
            'product_id' => 'required',
            'total' => 'required'
        ]);

        $validate['user_id'] = $request->user()->id;

        try {
            $cartItem = CartItem::where('product_id', $validate['product_id'])->where('user_id', $validate['user_id'])->first();
            $cartItem->update($validate);
            return ResponseFormatter::success(['cart_item' => $cartItem], 'Successs add total product');
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
}
