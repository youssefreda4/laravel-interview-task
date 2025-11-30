<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->successResponse(
            ProductResource::collection(Product::all()),
            __('Products retrieved successfully')
        );
    }
}
