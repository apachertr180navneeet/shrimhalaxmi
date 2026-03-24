<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'purchase_date',
        'pch_no',
        'bno',
        'vendor_id',
        'vendor_abbr',
        'remark',
        'freight',
        'total_qty_m',
        'total_net_meter',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_qty_m' => 'decimal:2',
        'total_net_meter' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
