<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortcutUser extends Model
{


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shortcut_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'shortcut_id',
        'sequence'
    ];
}
