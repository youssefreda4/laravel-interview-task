<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Return a success JSON response (with optional pagination).
     */
    protected function successResponse($data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return an error JSON response.
     */
    protected function errorResponse(string $message = 'Error', int $status = 400, $errors = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    /**
     * Handle all types of pagination (LengthAware, Cursor, Simple).
     */
    protected function paginatedResponse($paginator, $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        // Detect pagination type
        if ($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $pagination = [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'has_more' => $paginator->hasMorePages(),
            ];

            $links = [
                'next' => $paginator->nextPageUrl(),
                'prev' => $paginator->previousPageUrl(),
            ];

            $cursor = null;
        } elseif ($paginator instanceof \Illuminate\Pagination\CursorPaginator) {
            $pagination = [
                'per_page' => $paginator->perPage(),
                'has_more' => $paginator->hasMorePages(),
            ];

            $links = [
                'next' => $paginator->nextPageUrl(), // cursor pagination doesn't use URLs
                'prev' => $paginator->previousPageUrl(),
            ];

            $cursor = [
                'current' => optional($paginator->cursor())->encode(),
                'next' => optional($paginator->nextCursor())->encode(),
                'prev' => optional($paginator->previousCursor())->encode(),
            ];
        } else {
            // Handle SimplePaginator or custom paginator
            $pagination = [
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'has_more' => $paginator->hasMorePages(),
            ];

            $links = [
                'next' => $paginator->nextPageUrl(),
                'prev' => $paginator->previousPageUrl(),
            ];

            $cursor = null;
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'pagination' => $pagination,
            'links' => $links,
            'cursor' => $cursor,
        ], $status);
    }
}
