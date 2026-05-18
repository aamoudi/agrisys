<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkDetail extends Model
{
    /** @use HasFactory<\Database\Factories\WorkDetailFactory> */
    use HasFactory;
    protected $fillable = [
        'farm_id',
        'worker_id',
        'salary',
        'from_date',
        'to_date',
        'rate_num',
        'work_hours',
        'notes'
    ];


    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
