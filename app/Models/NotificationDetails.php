<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationDetails extends Model
{

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone',
        'notification_id',
        'status',
        'message'
    ];
}
