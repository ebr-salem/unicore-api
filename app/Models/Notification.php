<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'content',
        'sender_id',
        'related_type',
        'related_id',
    ];


    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Users who received this notification
    public function userNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }
}
