<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyKeyMiddleware
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('idempotency-key');

        if (!$key) {
            return $this->errorResponse('Idempotency key is required', 400);
        }

        $cacheKey = 'idem_' . $key;
        $lockKey  = 'idem_lock_' . $key;

        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        $lock = Cache::lock($lockKey, 10);

        if (!$lock->get()) {
            return $this->errorResponse('Request already in progress', 409);
        }

        try {
            $response = $next($request);

            if ($response->getStatusCode() < 500) {
                Cache::put($cacheKey, json_decode($response->getContent(), true), 60 * 60);
            }

            return $response;
        } finally {
            optional($lock)->release();
        }
    }
}
