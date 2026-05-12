<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $notifications
        ]);
    }

    public function markAsRead($id): JsonResponse
    {
        $notification = notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update(['llegida' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notificació marcada com a llegida'
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $notification = notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notificació eliminada'
        ]);
    }
}
