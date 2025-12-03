<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHoldRequest;
use App\Http\Resources\HoldResource;
use App\Models\Hold;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HoldController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $holds = Hold::with('product')->paginate();
        return $this->paginatedResponse(
            $holds,
            HoldResource::collection($holds),
            __('Holds retrieved successfully')
        );
    }

    public function show(Hold $hold)
    {
        return $this->successResponse(
            new HoldResource($hold),
            __('Hold retrieved successfully')
        );
    }

    public function store(StoreHoldRequest $request)
    {
        $validated = $request->validated();

        try {
            $hold = DB::transaction(function () use ($validated) {
                $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

                if ($product->stock < $validated['quantity']) {
                    throw new \Exception('Insufficient stock available');
                }

                $hold = Hold::create([
                    'product_id' => $product->id,
                    'quantity' => $validated['quantity'],
                    'expires_at' => now()->addMinutes(2),
                    'status' => 'active',
                ]);

                $product->decrement('stock', $validated['quantity']);

                Cache::forget("product_stock_{$product->id}");

                return $hold;
            });

            return $this->successResponse(
                new HoldResource($hold),
                __('Hold created successfully'),
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
