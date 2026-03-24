<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobWorkerInward extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'inward_date',
        'ch_no',
        'job_worker_id',
        'total_meter',
        'total_net_meter',
        'remark',
        'status',
    ];

    protected $casts = [
        'inward_date' => 'date',
        'total_meter' => 'decimal:2',
        'total_net_meter' => 'decimal:2',
    ];

    public function jobWorker()
    {
        return $this->belongsTo(JobWorker::class);
    }

    public function items()
    {
        return $this->hasMany(JobWorkerInwardItem::class)->orderBy('id');
    }
}
