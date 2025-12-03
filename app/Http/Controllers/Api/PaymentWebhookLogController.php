<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessWebhookRequest;
use App\Http\Resources\PaymentWebhookLogResource;
use App\Models\Order;
use App\Models\PaymentWebhookLog;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookLogController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $paymentWebhookLogs = PaymentWebhookLog::with('order')->paginate();
        return $this->paginatedResponse(
            PaymentWebhookLogResource::collection($paymentWebhookLogs),
            __('Payment webhook logs retrieved successfully')
        );
    }

    public function show(PaymentWebhookLog $paymentWebhookLog)
    {
        return $this->successResponse(
            new PaymentWebhookLogResource($paymentWebhookLog->load('order')),
            __('Payment webhook log retrieved successfully')
        );
    }

    public function process(ProcessWebhookRequest $request)
    {
        $validated = $request->validated();
        $idempotencyKey = $request->header('idempotency-key');
        $orderId = $validated['order_id'];
        $status = $validated['status'];

        try {
            $webhookLog = DB::transaction(function () use ($validated, $idempotencyKey, $orderId, $status) {

                $order = Order::lockForUpdate()->find($orderId);

                if (!$order) {
                    Log::warning("Order {$orderId} not found for webhook {$idempotencyKey}");

                    return PaymentWebhookLog::create([
                        'idempotency_key' => $idempotencyKey,
                        'order_id' => $orderId,
                        'status' => 'pending_order',
                        'payload' => $validated,
                        'processed_at' => now(),
                    ]);
                }

                $webhookLog = PaymentWebhookLog::create([
                    'idempotency_key' => $idempotencyKey,
                    'order_id' => $order->id,
                    'status' => $status,
                    'payload' => $validated,
                    'processed_at' => now(),
                ]);

                if ($status === 'success') {
                    $order->update(['status' => 'paid']);
                    Log::info("Order {$orderId} marked as paid");
                } elseif ($status === 'failed') {
                    $order->update(['status' => 'cancelled']);

                    $product = Product::lockForUpdate()->find($order->product_id);
                    $product->increment('stock', $order->quantity);

                    Cache::forget("product_stock_{$product->id}");

                    Log::info("Order {$orderId} cancelled, stock released");
                } else {
                    Log::warning("Unknown payment status '{$status}' for order {$orderId}");
                }

                return $webhookLog;
            });

            return $this->successResponse(
                new PaymentWebhookLogResource($webhookLog),
                __('Webhook processed successfully'),
                200
            );
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                $existingLog = PaymentWebhookLog::where('idempotency_key', $idempotencyKey)->first();

                return $this->successResponse(
                    new PaymentWebhookLogResource($existingLog),
                    __('Webhook already processed (race condition handled)'),
                    200
                );
            }

            throw $e;
        } catch (\Exception $e) {
            Log::error("Webhook processing failed: {$e->getMessage()}");

            return $this->errorResponse(
                __('Webhook processing failed'),
                500
            );
        }
    }
}
