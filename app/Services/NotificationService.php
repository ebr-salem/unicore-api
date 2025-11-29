<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Throwable;

class NotificationService
{
    protected FcmService $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Create a notification record and deliver it to the given user IDs.
     *
     * @param string $title
     * @param string|null $content
     * @param array|Collection $recipientUserIds
     * @param int|null $senderId
     * @param string|null $relatedType
     * @param int|null $relatedId
     * @param array $meta // any extra payload data
     *
     * @return Notification|null
     */
    public function createAndDispatch(
        string $title,
        ?string $content,
        $recipientUserIds,
        ?int $senderId = null,
        ?string $relatedType = null,
        ?int $relatedId = null,
        array $meta = []
    ): ?Notification {
        // normalize recipients to a collection of ints
        $recipients = collect($recipientUserIds)->filter()->map(fn($id) => (int) $id)->unique();

        if ($recipients->isEmpty()) {
            return null;
        }

        DB::beginTransaction();

        try {
            // 1) create notification record
            $notification = Notification::create([
                'title' => $title,
                'content' => $content,
                'sender_id' => $senderId,
                'related_type' => $relatedType,
                'related_id' => $relatedId,
            ]);

            // 2) filter recipients by their notification settings (receive_notifications)
            $allowedRecipientIds = $this->filterRecipientsBySettings($recipients);

            // 3) create user_notifications rows in bulk
            $bulk = $allowedRecipientIds->map(fn($userId) => [
                'notification_id' => $notification->id,
                'user_id' => $userId,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ])->values()->all();

            if (!empty($bulk)) {
                UserNotification::insert($bulk);
            }

            // 4) prepare FCM payload and send via FcmService
            if ($allowedRecipientIds->isNotEmpty()) {
                $payload = array_merge([
                    'title' => $title,
                    'body' => $content,
                    'notification_id' => $notification->id,
                    'related_type' => $relatedType,
                    'related_id' => $relatedId,
                ], $meta);

                // FcmService should handle token lookup / topics internally.
                $this->fcmService->sendToUsers($allowedRecipientIds->all(), $payload);
            }

            DB::commit();

            return $notification;
        } catch (Throwable $e) {
            DB::rollBack();
            // You can log the exception or rethrow
            report($e);
            return null;
        }
    }

    /**
     * Create a notification and send it to all students of a department/year/batch.
     * (helper — implement lookup logic as needed)
     *
     * @param string $title
     * @param string|null $content
     * @param array $filters // e.g. ['department_id' => 1, 'academic_year_id' => 2]
     * @param int|null $senderId
     */
    public function createForAcademicFilter(string $title, ?string $content, array $filters = [], ?int $senderId = null, ?string $relatedType = null, ?int $relatedId = null, array $meta = [])
    {
        // Example: adapt according to your DB schema for student assignment table
        $query = User::query()->where('role', 'student')->whereHas('studentAcademicAssignment', function ($q) use ($filters) {
            foreach ($filters as $k => $v) {
                $q->where($k, $v);
            }
        });

        $userIds = $query->pluck('id')->all();

        return $this->createAndDispatch($title, $content, $userIds, $senderId, $relatedType, $relatedId, $meta);
    }

    /**
     * Filter recipients by NotificationSetting.receive_notifications (default true).
     *
     * @param Collection $recipientIds
     * @return Collection
     */
    protected function filterRecipientsBySettings(Collection $recipientIds): Collection
    {
        // Get users who explicitly have receive_notifications = false
        $disabled = NotificationSetting::whereIn('user_id', $recipientIds)
            ->where('receive_notifications', false)
            ->pluck('user_id')
            ->all();

        return $recipientIds->diff($disabled)->values();
    }

    /**
     * Mark a specific user_notification as read.
     *
     * @param int $userNotificationId
     * @param int $userId
     * @return bool
     */
    public function markAsRead(int $userNotificationId, int $userId): bool
    {
        $un = UserNotification::where('id', $userNotificationId)
            ->where('user_id', $userId)
            ->first();

        if (!$un) {
            return false;
        }

        $un->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark all notifications for a user as read.
     *
     * @param int $userId
     * @return int number of rows updated
     */
    public function markAllAsRead(int $userId): int
    {
        return UserNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
                'updated_at' => now(),
            ]);
    }
}
