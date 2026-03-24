<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $fillable = [
        'item_id',
        'name',
        'status',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}