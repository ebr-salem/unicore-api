<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Auth;

class NotificationSettingsController extends Controller
{
    /** GET     /notification-settings */
    public function index()
    {
        $user = Auth::user();

        $settings = NotificationSetting::firstOrCreate(
            ['user_id' => $user->id],
            ['receive_notifications' => true]
        );

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /** PUT     /notification-settings */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'receive_notifications' => 'required|boolean'
        ]);

        $settings = NotificationSetting::updateOrCreate(
            ['user_id' => $user->id],
            ['receive_notifications' => $request->receive_notifications]
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated',
            'data' => $settings
        ]);
    }
}
