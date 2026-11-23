<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /** Standard JSON envelope: { "data": ..., "message": ... } */
    protected function ok(mixed $data = null, ?string $message = null, int $status = 200, array $extra = []): JsonResponse
    {
        return response()->json(array_filter([
            'data' => $data,
            'message' => $message,
        ], fn ($v) => $v !== null) + $extra, $status);
    }

    /** Paginated list: { "data": [...], "meta": {...}, ...extra } */
    protected function paginated(LengthAwarePaginator $page, array $extra = []): JsonResponse
    {
        return response()->json([
            'data' => $page->items(),
            'meta' => [
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ],
        ] + $extra);
    }

    /** Clamp the "sort_by" / "sort_order" query parameters to a whitelist. */
    protected function sorting(array $allowed, string $default): array
    {
        $by = request('sort_by');
        $order = strtolower((string) request('sort_order')) === 'desc' ? 'desc' : 'asc';

        return [in_array($by, $allowed, true) ? $by : $default, $order];
    }
}
