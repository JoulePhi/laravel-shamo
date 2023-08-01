<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseFormatter;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->id;
        $name = $request->name;
        $limit = $request->limit;
        $show_product = $request->show_product;

        if ($id) {
            $category = Category::with(['products'])->find($id);
            if ($category) {
                return ResponseFormatter::success($category, 'Success get list of categories');
            }
            return ResponseFormatter::error('Failed get list of categories', 404);
        }

        $category = Category::query();
        if ($name) {
            $category->where('name', 'like', '%' . $name . '%');
        }

        if ($show_product) {
            $category->with(['products']);
        }


        return ResponseFormatter::success(
            $category->paginate($limit),
            'Success get list of categories'
        );
    }
}
