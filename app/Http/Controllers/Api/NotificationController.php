<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->latest()
            ->limit(30)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'type' => $n->data['type'] ?? null,
                'data' => $n->data,
                'read' => ! is_null($n->read_at),
                'created_at' => $n->created_at,
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markRead(string $id): JsonResponse
    {
        Auth::user()->notifications()->findOrFail($id)->markAsRead();

        return response()->json(['message' => 'Marked as read.']);
    }

    public function markAllRead(): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read.']);
    }
}
