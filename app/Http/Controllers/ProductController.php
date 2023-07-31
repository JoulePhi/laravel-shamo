<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseFormatter;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->id;
        $name = $request->name;
        $description = $request->description;
        $tags = $request->tags;
        $price_from = $request->price_from;
        $price_to = $request->price_to;
        $category = $request->category;
        $limit = $request->limit;
        if ($id) {
            $products = Product::with(['category', 'galleries'])->find($id);
            if ($products) {
                return ResponseFormatter::success($products, 'Success get list of products');
            }
            return ResponseFormatter::error('Failed get list of products', 404);
        }

        $products = Product::with(['category', 'galleries']);
        if ($name) {
            $products->where('name', 'like', '%' . $name . '%');
        }
        if ($description) {
            $products->where('description', 'like', '%' . $description . '%');
        }
        if ($tags) {
            $products->where('tags', 'like', '%' . $tags . '%');
        }
        if ($price_from) {
            $products->where('price', '>=', $price_from);
        }
        if ($price_to) {
            $products->where('price', '<=', $price_to);
        }
        if ($category) {
            $products->where('category', $category);
        }

        return ResponseFormatter::success(
            $products->paginate($limit),
            'Success get list of products'
        );
    }
}
