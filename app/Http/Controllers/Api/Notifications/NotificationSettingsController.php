<?php
namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;

class NotificationSettingsController extends Controller
{
    public function show(Request $request)
    {
        $settings = NotificationSetting::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message'  => 'Notification settings retrieved successfully',
            'settings' => $settings,
        ]);

    }

    public function update(Request $request)
    {
        $request->validate([
            'receive_notifications' => 'required|boolean',
        ]);

        $settings = NotificationSetting::updateOrCreate(
            ['user_id' => $request->user()->id],
            ['receive_notifications' => $request->receive_notifications]
        );

        return response()->json([
            'message'  => 'Notification settings updated successfully',
            'settings' => $settings,
        ]);

    }
}
