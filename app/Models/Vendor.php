<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vendor_name',
        'firm_name',
        'abbr',
        'phone',
        'email',
        'address',
        'gst_no',
        'city',
        'state',
        'pincode',
        'status'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
