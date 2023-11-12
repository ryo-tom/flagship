<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductSearchRequest;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(ProductSearchRequest $request): Response
    {
        $keyword    = $request->input('keyword', '');
        $productsQuery     = Product::query()->with(['category.group'])->searchByKeyword($keyword);
        $productsPaginator = $productsQuery->paginate(100)->withQueryString();

        return Inertia::render('Product/Index', [
            'productsPaginator' => $productsPaginator,
        ]);
    }
}
