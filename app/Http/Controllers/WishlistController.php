<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseFormatter;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function add(Request $request)
    {
        $validate = $request->validate([
            'wishlist_id' => 'required',
            'product_id' => 'required',
        ]);

        try {
            $wishlistItem = WishlistItem::create($validate);
            return ResponseFormatter::success(['wishlist_item' => $wishlistItem], 'Successs add product to wishlist');
        } catch (\Exception $err) {
            return ResponseFormatter::error(
                'Failed add product to wishlist',
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
        $wishlists = Wishlist::with(['wishlistItems.products'])->where('user_id', $request->user()->id)->first();
        return ResponseFormatter::success($wishlists->wishlistItems, 'Success get list of wishlist items');
    }
}
