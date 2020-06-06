<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationDraft extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification_drafts';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'title',
        'body'
    ];


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
            NotificationUser::class,
            'notification_user',
            'notification_id',
            'user_id'
        )->withTimestamps();
    }


}
