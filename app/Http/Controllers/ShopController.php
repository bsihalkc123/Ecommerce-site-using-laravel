<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        /* -------------------------
         | Pagination & Sorting
         |--------------------------*/
        $size  = $request->query('size', 12);
        $order = $request->query('order', -1);

        $o_column = 'id';
        $o_order  = 'DESC';

        switch ($order) {
            case 1:
                $o_column = 'created_at';
                $o_order  = 'DESC';
                break;
            case 2:
                $o_column = 'created_at';
                $o_order  = 'ASC';
                break;
            case 3:
                $o_column = 'regular_price';
                $o_order  = 'ASC';
                break;
            case 4:
                $o_column = 'regular_price';
                $o_order  = 'DESC';
                break;
        }

        /* -------------------------
         | Filters
         |--------------------------*/
        $f_brands     = $request->query('brands');      // "1,2"
        $f_categories = $request->query('categories');  // "3,4"
        $min_price    = $request->query('min_price');
        $max_price    = $request->query('max_price');

        /* -------------------------
         | Sidebar Data
         |--------------------------*/
        $brands     = Brand::orderBy('name', 'ASC')->get();
        $categories = Category::orderBy('name', 'ASC')->get();

        /* -------------------------
         | Product Query
         |--------------------------*/
        $products = Product::query();

        // ✅ Brand filter
        if (!empty($f_brands)) {
            $products->whereIn('brand_id', explode(',', $f_brands));
        }

        // ✅ Category filter
        if (!empty($f_categories)) {
            $products->whereIn('category_id', explode(',', $f_categories));
        }

        // ✅ Price filter
        if ($min_price !== null && $max_price !== null) {
            $products->whereRaw("
                IF(
                    sale_price IS NOT NULL AND sale_price > 0,
                    sale_price,
                    regular_price
                ) BETWEEN ? AND ?
            ", [$min_price, $max_price]);
        }



        /* -------------------------
         | Final Query
         |--------------------------*/
        $products = $products
            ->orderBy($o_column, $o_order)
            ->paginate($size);

        return view('shop', compact(
            'products',
            'size',
            'order',
            'brands',
            'f_brands',
            'categories',
            'f_categories',
            'min_price',
            'max_price'
        ));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->firstOrFail();

        $products = Product::where('slug', '<>', $product_slug)
            ->latest()
            ->take(8)
            ->get();

        return view('details', compact('product', 'products'));
    }
}
