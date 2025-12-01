<?php
namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    /**
     * Create a public notification
     */
    public function create($title, $content, $relatedType = null, $relatedId = null, $senderId = null)
    {
        return Notification::create([
            'title'        => $title,
            'content'      => $content,
            'related_type' => $relatedType,
            'related_id'   => $relatedId,
            'sender_id'    => $senderId,
        ]);
    }

    /**
     * Create a notification & assign to users
     */
    public function createForUsers(array $userIds, string $title, string $content, $relatedType = null, $relatedId = null, $senderId = null)
    {
        // 1) Create notification in main table
        $notification = Notification::create([
            'title'        => $title,
            'content'      => $content,
            'sender_id'    => $senderId,
            'related_type' => $relatedType,
            'related_id'   => $relatedId,
        ]);

        // 2) Create user_notifications rows (only for users who enabled notifications)
        $eligibleUsers = User::whereIn('id', $userIds)
            ->whereHas('notificationSettings', function ($q) {
                $q->where('receive_notifications', true);
            })
            ->get();

        foreach ($eligibleUsers as $user) {
            UserNotification::create([
                'notification_id' => $notification->id,
                'user_id'         => $user->id,
            ]);

            // Optional — send FCM (if you want real-time)
            if ($user->fcm_token ?? false) {
                $this->sendFCM(
                    $user->fcm_token,
                    $title,
                    $content
                );
            }
        }

        return $notification;
    }

    /**
     * Return all notifications for a user
     */
    public function getUserNotifications(User $user)
    {
        return UserNotification::with('notification.sender')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mark one notification as read
     */
    public function markAsRead(User $user, $id)
    {
        $userNotification = UserNotification::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $userNotification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $userNotification;
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(User $user)
    {
        return UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Send notification using Firebase Cloud Messaging
     */
    public function sendFCM($token, $title, $body)
    {
        $serverKey = config('services.fcm.server_key');

        if (! $serverKey || ! $token) {
            return;
        }

        Http::withHeaders([
            'Authorization' => "key={$serverKey}",
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to'           => $token,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
            'data'         => [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ]);
    }
}
