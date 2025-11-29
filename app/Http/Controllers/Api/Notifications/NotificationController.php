<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /** GET    /notifications */
    public function index()
    {
        $user = Auth::user();

        $notifications = UserNotification::with('notification.sender')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /** PATCH   /notifications/{notification_id}/read */
    public function markAsRead($id)
    {
        $user = Auth::user();

        $userNotification = UserNotification::where('user_id', $user->id)
            ->where('notification_id', $id)
            ->firstOrFail();

        $userNotification->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /** PATCH   /notifications/read-all */
    public function markAllAsRead()
    {
        $user = Auth::user();

        UserNotification::where('user_id', $user->id)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }
}
