<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_name',
        'abbr',
        'remark',
        'status',
        'stock_qty_m',
        'stock_net_meter',
    ];

    protected $casts = [
        'stock_qty_m' => 'decimal:2',
        'stock_net_meter' => 'decimal:2',
    ];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Increase stock when items are purchased or returned from job work
     */
    public function increaseStock(float $qtyM, float $netMeter): void
    {
        $this->increment('stock_qty_m', $qtyM);
        $this->increment('stock_net_meter', $netMeter);
    }

    /**
     * Decrease stock when items are assigned to job workers
     */
    public function decreaseStock(float $qtyM, float $netMeter): void
    {
        $this->decrement('stock_qty_m', $qtyM);
        $this->decrement('stock_net_meter', $netMeter);
    }

    /**
     * Check if sufficient stock is available
     */
    public function hasSufficientStock(float $qtyM, float $netMeter): bool
    {
        return $this->stock_qty_m >= $qtyM && $this->stock_net_meter >= $netMeter;
    }
}
