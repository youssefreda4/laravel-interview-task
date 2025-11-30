<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentWebhookLogResource;
use App\Models\PaymentWebhookLog;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class PaymentWebhookLogController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->successResponse(
            PaymentWebhookLogResource::collection(PaymentWebhookLog::all()),
            __('Payment webhook logs retrieved successfully')
        );
    }

    public function show(PaymentWebhookLog $paymentWebhookLog)
    {
        return $this->successResponse(
            new PaymentWebhookLogResource($paymentWebhookLog),
            __('Payment webhook log retrieved successfully')
        );
    }
}
