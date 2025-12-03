<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Hold;
use App\Models\Order;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $orders = Order::with(['product', 'hold'])->paginate();
        return $this->paginatedResponse(
            $orders,
            OrderResource::collection($orders),
            __('Orders retrieved successfully')
        );
    }

    public function show(Order $order)
    {
        return $this->successResponse(
            new OrderResource($order->load(['product', 'hold'])),
            __('Order retrieved successfully')
        );
    }

     public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        try {
            $order = DB::transaction(function () use ($validated) {
                
                $hold = Hold::lockForUpdate()->findOrFail($validated['hold_id']);

                if ($hold->status !== 'active') {
                    throw new \Exception('Hold has already been used or expired');
                }

                if ($hold->expires_at < now()) {
                    throw new \Exception('Hold has expired');
                }

                $order = Order::create([
                    'hold_id' => $hold->id,
                    'product_id' => $hold->product_id,
                    'quantity' => $hold->quantity,
                    'total_price' => $hold->quantity * $hold->product->price,
                    'status' => 'pending',
                ]);

                $hold->update(['status' => 'used']);

                return $order;
            });

            return $this->successResponse(
                new OrderResource($order),
                __('Order created successfully'),
                201
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                422
            );
        }
    }
}
