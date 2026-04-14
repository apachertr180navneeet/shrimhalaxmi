<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'abbr',
        'phone',
        'email',
        'firm_name',
        'gst_no',
        'location',
        'address_2',
        'state',
        'status',
    ];
}
