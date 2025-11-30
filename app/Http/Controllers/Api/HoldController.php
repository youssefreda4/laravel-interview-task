<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HoldResource;
use App\Models\Hold;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class HoldController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->successResponse(
            HoldResource::collection(Hold::all()),
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
}
