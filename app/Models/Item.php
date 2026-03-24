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
    ];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
