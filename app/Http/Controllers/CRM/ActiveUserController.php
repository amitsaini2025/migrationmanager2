<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Services\ActiveUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActiveUserController extends Controller
{
    public function __construct(
        protected ActiveUserService $activeUsers
    ) {
        $this->middleware('auth:admin');
    }

    /**
     * Return the list of currently active users.
     */
    public function index(Request $request): JsonResponse
    {
        $threshold = (int) $request->query('threshold', 5);

        $data = $this->activeUsers
            ->getActiveUsers(max(1, $threshold))
            ->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'threshold_minutes' => $threshold,
                'generated_at' => now()->toIso8601String(),
            ],
        ]);
    }
}


