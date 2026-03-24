<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobWorkAssignmentItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'job_work_assignment_id',
        'purchase_item_id',
        'item_id',
        'sort_order',
        'lot_no',
        'quality',
        'meter',
        'fold',
        'net_meter',
        'process',
        'lr_no',
        'transport',
    ];

    protected $casts = [
        'meter' => 'decimal:2',
        'fold' => 'decimal:2',
        'net_meter' => 'decimal:2',
    ];

    public function assignment()
    {
        return $this->belongsTo(JobWorkAssignment::class, 'job_work_assignment_id');
    }

    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
