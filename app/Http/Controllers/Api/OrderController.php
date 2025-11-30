<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Traits\ApiResponseTrait;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->successResponse(
            OrderResource::collection(Order::all()),
            __('Orders retrieved successfully')
        );
    }
}
