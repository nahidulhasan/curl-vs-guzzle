<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpConfig extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'otp_configs';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token_length_number','token_length_string','validation_time'
    ];
}
