<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Push\PushService;
use Illuminate\Http\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SseController extends Controller
{
    public function stream(LoggerInterface $logger, PushService $service): StreamedResponse|JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!$user->is_confirmed) {
            return response()->json([
                'error' => 'User not confirmed',
            ], 403);
        }

        return response()->stream(function () use ($logger, $service, $user) {
            ob_start();
            if (ob_get_level()) {
                ob_clean();
            }
            try {
                $start = time();
                $pingInterval = 10;
                $lastPing = time();
                $maxDuration = 60;

                while (time() - $start < $maxDuration) {
                    $events = $service->getUserEvents($user->id);
                    foreach ($events as $event) {
                        echo "id: {$event['id']}\n";
                        echo "event: {$event['type']}\n";
                        echo "data: " . json_encode($event['data']) . "\n\n";
                        ob_flush();
                        flush();
                    }

                    // Keeping the connection alive
                    if (time() - $lastPing >= $pingInterval) {
                        echo "event: ping\ndata: {}\n\n";
                        ob_flush();
                        flush();
                        $lastPing = time();
                    }
                    sleep(2);
                }
            } catch (\Throwable $e) {
                $logger->error('SSE stream error', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                echo "event: error\ndata: \"Server error: {$e->getMessage()}\"\n\n";
                ob_flush();
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }
}
