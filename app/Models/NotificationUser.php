<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationUser extends Model
{
    protected $guarded = ['id'];
    protected $table = 'notification_user';

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'id');
    }

    public function scopeUnread($builder)
    {
        return $builder->where('is_read', 0);
    }


    public function scopeUnseen($builder)
    {
        return $builder->where('is_seen', 0);
    }
}
