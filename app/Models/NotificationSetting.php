<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'receive_notifications',
    ];

    protected $casts = [
        'receive_notifications' => 'boolean',
    ];

    // Each setting belongs to a user 
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
