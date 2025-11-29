<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class FcmService
{
    /**
     * Send notification to a list of users using their FCM tokens.
     *
     * @param array $userIds
     * @param array $payload [title, body, ...]
     * @return void
     */
    public function sendToUsers(array $userIds, array $payload): void
    {
        $serverKey = config('services.fcm.server_key');

        if (! $serverKey) {
            return;
        }

        // 1) Fetch tokens from users table
        $tokens = User::whereIn('id', $userIds)
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->all();

        if (empty($tokens)) {
            return; // no tokens, no push notifications
        }

        // 2) Prepare FCM request format
        $data = [
            "registration_ids" => $tokens,
            "notification"     => [
                "title" => $payload['title'] ?? 'New Notification',
                "body"  => $payload['body'] ?? '',
            ],
            "data"             => $payload, // custom data sent with notification
        ];

        // 3) Send request to Firebase
        Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type'  => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', $data);
    }
}
