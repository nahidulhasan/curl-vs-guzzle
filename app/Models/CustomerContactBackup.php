<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerContactBackup extends Model
{
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
