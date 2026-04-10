<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobWorkerInwardItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'job_worker_inward_id',
        'item_id',
        'lot_no',
        'source_lot_no',
        'quality',
        'meter',
        'fold',
        'total_meter',
        'shrinkage',
        'type',
        'after_shrinkage_meter',
    ];

    protected $casts = [
        'meter' => 'decimal:2',
        'fold' => 'decimal:2',
        'total_meter' => 'decimal:2',
    ];

    public function inward()
    {
        return $this->belongsTo(JobWorkerInward::class, 'job_worker_inward_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
