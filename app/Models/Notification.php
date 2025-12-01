<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'content',
        'sender_id',
        'related_type',
        'related_id',
    ];

    /** Notification sender (User) */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /** Connect Notification → User(s) */
    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    /** Polymorphic relation: link to lecture / task / quiz / post… */
    public function related()
    {
        return $this->morphTo(null, 'related_type', 'related_id');
    }
}
