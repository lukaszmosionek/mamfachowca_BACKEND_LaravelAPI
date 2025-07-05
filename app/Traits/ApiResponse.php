<?php

namespace App\Traits;

trait ApiResponse {
    protected function success($data = null, $message = '', $code = 200) {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    protected function error($message = '', $code = 500, $errors = []) {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
