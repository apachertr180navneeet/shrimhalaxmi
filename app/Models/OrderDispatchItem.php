<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDispatchItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_dispatch_id',
        'item_id',
        'lot_no',
        'item_code',
        'meter',
        'rate',
        'amount',
        'sort_order',
    ];

    protected $casts = [
        'meter' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function orderDispatch()
    {
        return $this->belongsTo(OrderDispatch::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
