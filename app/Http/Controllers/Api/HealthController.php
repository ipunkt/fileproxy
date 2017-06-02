<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class HealthController extends ApiController
{
    public function health()
    {
        if (file_put_contents(storage_path('health.txt'), date('Y-m-d H:i:s')) === false) {
            return $this->respond(503, 'storage unavailable');
        }

        return $this->respond(200, 'healthy');
    }

    /**
     * responds given data as json
     *
     * @param int $code
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    private function respond(int $code, string $message): JsonResponse
    {
        $data = [
            'status' => $code,
            'message' => $message,
        ];

        return $this->respondData($data, $code);
    }
}
