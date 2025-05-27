<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'address1',
        'address2',
        'pincode',
        'city',
        'state',
        'type',
    ];

}
