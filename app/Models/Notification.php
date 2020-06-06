<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /*
    |--------------------------------------------------------------------------
    | CONSTANTS
    |--------------------------------------------------------------------------
    */
    const SUCCESSFUL = 'SUCCESSFUL';
    const FAILED = 'FAILED';
    const PERTIALLY_SUCCESSFULL = 'PERTIALLY_SUCCESSFULL';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'category_slug',
        'title',
        'body',
        'category_slug',
        'category_name'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(NotificationDetails::class);
    }

    public function category()
    {
        return $this->belongsTo(NotificationCategory::class, 'category_slug', 'slug');
    }

    public function receivers()
    {
        return $this->belongsToMany(
            'App\Models\User',
            'notification_user',
            'notification_id',
            'user_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function NotificationCategory()
    {
        return $this->belongsTo(NotificationCategory::class, 'category_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            Notification::class,
            'notification_user',
            'notification_id',
            'user_id'
        )->withTimestamps();
    }
}
