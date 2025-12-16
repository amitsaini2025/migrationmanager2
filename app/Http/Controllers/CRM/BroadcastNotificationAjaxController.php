<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Services\BroadcastNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class BroadcastNotificationAjaxController extends Controller
{
    public function __construct(
        protected BroadcastNotificationService $broadcasts
    ) {
        $this->middleware('auth:admin');
    }

    protected function sender(Request $request)
    {
        return $request->user('admin');
    }

    public function store(Request $request): JsonResponse
    {
        \Log::info('📢 Broadcast send request received', [
            'user_id' => $this->sender($request)?->id,
            'payload' => $request->except(['_token'])
        ]);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'title' => ['nullable', 'string', 'max:255'],
            'scope' => ['required', Rule::in(['all', 'specific', 'team'])],
            'recipient_ids' => ['required_if:scope,specific', 'array'],
            'recipient_ids.*' => ['integer'],
        ]);

        \Log::info('✅ Broadcast validation passed', ['validated' => $validated]);

        try {
            $result = $this->broadcasts->createBroadcast([
                'sender' => $this->sender($request),
                'message' => $validated['message'],
                'title' => $validated['title'] ?? null,
                'scope' => $validated['scope'],
                'recipient_ids' => $validated['recipient_ids'] ?? [],
            ]);
            
            \Log::info('✅ Broadcast created successfully', [
                'batch_uuid' => $result['batch_uuid'],
                'recipient_count' => $result['recipient_count']
            ]);
        } catch (\InvalidArgumentException $exception) {
            \Log::error('❌ Broadcast creation failed', [
                'error' => $exception->getMessage()
            ]);
            
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'status' => 'sent',
            'data' => $result,
        ], Response::HTTP_CREATED);
    }

    public function history(Request $request): JsonResponse
    {
        $senderId = (int) $this->sender($request)->id;
        $history = $this->broadcasts->getBroadcastHistory($senderId);

        return response()->json([
            'data' => $history,
        ]);
    }

    public function details(Request $request, string $batchUuid): JsonResponse
    {
        try {
            $details = $this->broadcasts->getBroadcastDetails($batchUuid, (int) $this->sender($request)->id);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $details,
        ]);
    }

    public function unread(Request $request): JsonResponse
    {
        $unread = $this->broadcasts->getUnreadBroadcasts((int) $this->sender($request)->id);

        return response()->json([
            'data' => $unread,
        ]);
    }

    public function markAsRead(Request $request, int $notificationId): JsonResponse
    {
        $updated = $this->broadcasts->markAsRead($notificationId, (int) $this->sender($request)->id);

        if (!$updated) {
            return response()->json([
                'message' => 'Notification not found or already read.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }
}


