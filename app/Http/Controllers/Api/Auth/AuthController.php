<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function login(LoginRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $user = User::where('mobile', $validatedData['mobile'])->first();

            if (! $user || ! Hash::check($validatedData['password'], $user->password)) {
                return $this->errorResponse(__('Mobile number or password is incorrect'), 401);
            }

            $token = $user->createToken(
                'token',
                ['*'],
                now()->addMonth(2)
            )->plainTextToken;

            return $this->successResponse(
                [
                    'user' => new UserResource($user),
                    'token' => $token,
                ],
                __('Login successful'),
                200
            );
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(__('Login failed'), 500);
        }
    }
}
