<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class HealthController extends ApiController
{
    public function health()
    {
        $mountedCheckFile = storage_path('app/mounted');
        if(!@file_exists($mountedCheckFile))
            return $this->respond(503, 'storage unavailable');

        $healthCheckFile = storage_path('health.txt');

        if (@file_put_contents($healthCheckFile, date('Y-m-d H:i:s')) === false) {
            return $this->respond(503, 'storage unavailable');
        }

        @unlink($healthCheckFile);

        return $this->respond(200, 'healthy');
    }

    /**
     * responds given data as json.
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
