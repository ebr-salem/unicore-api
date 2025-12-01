<?php
namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /notifications
     * List all notifications for user
     */
    public function index(Request $request)
    {
        $notifications = $this->service->getUserNotifications($request->user());

        return response()->json([
            'message'       => 'User notifications retrieved successfully',
            'notifications' => $notifications,
        ]);

    }

    /**
     * GET /notifications/unread-count
     */
    public function unreadCount(Request $request)
    {
        $count = UserNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'message'      => 'Unread notifications count retrieved',
            'unread_count' => $count,
        ]);

    }

    /**
     * PATCH /notifications/{user_notification_id}/read
     */
    public function markRead(Request $request, $id)
    {
        $this->service->markAsRead($request->user(), $id);

        return response()->json([
            'message' => 'Notification marked as read for this user',
        ]);

    }

    /**
     * PATCH /notifications/read-all
     */
    public function markAllRead(Request $request)
    {
        $this->service->markAllAsRead($request->user());

        return response()->json([
            'message' => 'All notifications marked as read for this user',
        ]);

    }

    /**
     * DELETE /notifications/{user_notification_id}
     */
    public function destroy(Request $request, $id)
    {
        UserNotification::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->delete();

        return response()->json([
            'message' => 'Notification deleted for this user',
        ]);

    }

    /**
     * DELETE /notifications
     */
    public function destroyAll(Request $request)
    {
        UserNotification::where('user_id', $request->user()->id)
            ->delete();

        return response()->json([
            'message' => 'All notifications deleted for this user',
        ]);

    }
}
