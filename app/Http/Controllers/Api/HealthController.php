<?php

namespace App\Http\Controllers\Api;

class HealthController extends ApiController
{
    public function health()
    {
        if (file_put_contents(storage_path('health.txt'), date('Y-m-d H:i:s')) === false) {
            return $this->respond(503, 'storage unavailable');
        }

        return $this->respond(200, 'healthy');
    }

    private function respond($code, $message)
    {
        $data = [
            'status' => $code,
            'message' => $message,
        ];

        return $this->respondData($data, $code);
    }
}
