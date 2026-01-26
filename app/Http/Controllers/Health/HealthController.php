<?php

namespace App\Http\Controllers\Health;

use App\Enums\Enums\StatusCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            // Test DB connection
            \DB::connection()->getPdo();

            return $this->success([
                'status' => 'ok',
                'timestamp' => now()->toIso8601String(),

            ], 'Health check successful');
        } catch (\Exception $e) {
            return $this->failed([
                'status' => 'error',
                'timestamp' => now()->toIso8601String(),
            ], StatusCode::InternalServerError->value, 'Database connection failed');
        }

    }
}
