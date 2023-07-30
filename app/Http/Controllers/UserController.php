<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helper\ResponseFormatter;
use App\Models\Cart;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validate = $request->validate(
                [
                    'fullname' => ['required', 'string', 'max:255'],
                    'username' => ['required', 'string', 'max:255', 'unique:users'],
                    'email' => ['required', 'email', 'max:255', 'unique:users'],
                    'phone_number' => ['nullable', 'string', 'max:255'],
                    'password' => ['required', 'string', new Password],
                ]
            );
            $validate['password'] = Hash::make($validate['password']);
            $user = User::create($validate);
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            $cart = Cart::create([
                'user_id' => $user->id
            ]);
            $wishlist = Wishlist::create([
                'user_id' => $user->id
            ]);
            return ResponseFormatter::success(
                [
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user,
                    'cart_id' => $cart->id,
                    'wishlist_id' => $wishlist->id,
                ],
                'User Registered'
            );
        } catch (\Exception $err) {
            return ResponseFormatter::error(
                'User Failed To Register',
                500,
                [
                    'message' => 'Something went wrong',
                    'error' => $err->getMessage()
                ]
            );
        }
    }

    public function login(Request $request)
    {
        try {
            $validate = $request->validate(
                [
                    'email' => ['required', 'email', 'max:255'],
                    'password' => ['required', 'string', new Password],
                ]
            );
            $credent = request(['email', 'password']);
            if (!Auth::attempt($credent)) {
                return ResponseFormatter::error(
                    'Authentication Failed',
                    500,
                    [
                        'message' => 'Unauthorized',
                    ]
                );
            }
            $user = User::with(['cart', 'wishlist'])->where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password)) {
                throw new \Exception('Invalid Credential');
            }
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success(
                [
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user,
                ],
                'Authenticated'
            );
        } catch (\Exception $err) {
            return ResponseFormatter::error(
                'Authentication Failed',
                500,
                [
                    'message' => 'Something went wrong',
                    'error' => $err->getMessage()
                ]
            );
        }
    }

    public function fetch(Request $request)
    {
        $user = User::with(['cart', 'wishlist'])->where('id', $request->user()->id)->first();
        return ResponseFormatter::success(
            $user,
            'Success Get User'
        );
    }

    public function update(Request $request)
    {
        $validate = $request->validate(
            [
                'fullname' => ['string', 'max:255'],
                'username' => ['string', 'max:255', 'unique:users'],
                'email' => ['email', 'max:255', 'unique:users'],
                'phone_number' => ['nullable', 'string', 'max:255'],
                'password' => ['string', new Password],
            ]
        );

        $user = Auth::user();
        $user->update($validate);
        return ResponseFormatter::success($user, 'success update data');
    }


    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
    }
}


// 1|QcWvI85x795jkHLuuHvI6YEpv1wyxAmkIZSfI72u