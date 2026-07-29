<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait ResponseFormat
{
    /**
     * Format untuk respond sukes.
     */
    public function responseSuccess(string $message, $data = null, $code = 200, $meta = null)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $code);
    }

    /**
     * Format untuk respond sukes dengan halaman.
     */
    public function responseSuccessPagination(string $message, LengthAwarePaginator $result, $code = 200, $meta = null)
    {
        $meta = [
            'current_page' => $result->currentPage(),
            'last_page' => $result->lastPage(),
            'per_page' => $result->perPage(),
            'total' => $result->total(),
        ];
        $data = $result->items();
        return response()->json([
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $code);
    }

    /**
     * Format untuk respond eror.
     */
    public function responseError(string $message, $data = null, $code = 400)
    {

        return response()->json([
            'message' => $message,
            'data' => [
                'error' => $data
            ]
        ], $code);
    }
}
