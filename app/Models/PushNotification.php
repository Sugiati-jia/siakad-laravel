<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PushNotification extends Model
{
    use HasFactory;

    protected $table = 'push_notifications';

    protected $fillable = [
        'title',
        'body',
        'icon',
        'url',
        'target_role',
        'target_user_id',
        'sent_by',
        'delivered_count',
    ];

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class, 'notification_id');
    }
}
