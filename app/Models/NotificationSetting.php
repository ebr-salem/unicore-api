<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'receive_notifications',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
