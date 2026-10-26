<?php

namespace App\Http\Controllers;

use App\Services\HealthService;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __construct(private readonly HealthService $health) {}

    public function __invoke(): JsonResponse
    {
        $report = $this->health->check();
        $healthy = $this->health->healthy($report);

        return response()->json([
            'success' => $healthy,
            'message' => null,
            'data' => $report,
        ], $healthy ? 200 : 503);
    }
}
