<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDispatch extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'dispatch_date',
        'dispatch_no',
        'bill_no',
        'customer_id',
        'mobile_number',
        'transport',
        'status',
        'total_meter',
        'total_amount',
    ];

    protected $casts = [
        'dispatch_date' => 'date',
        'total_meter' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderDispatchItem::class);
    }
}
