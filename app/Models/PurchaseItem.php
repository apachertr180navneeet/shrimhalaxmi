<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'purchase_id',
        'item_id',
        'item_code',
        'sort_order',
        'lot_no',
        'lot_roll_no',
        'color',
        'qty_m',
        'fold',
        'rate',
        'transport',
        'lr_no',
        'net_meter',
        'amount',
    ];

    protected $casts = [
        'qty_m' => 'decimal:2',
        'fold' => 'decimal:2',
        'rate' => 'decimal:2',
        'net_meter' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
