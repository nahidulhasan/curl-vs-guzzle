<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UssdCode extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ussd_codes';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'code',
        'purpose',
        'provider',
    ];

}
