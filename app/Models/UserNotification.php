<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = [
        'notification_id',
        'user_id',
        'is_read',
        'read_at',
    ];

    /** Belongs to Notification */
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    /** Belongs to User */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
