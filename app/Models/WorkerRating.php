<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerRating extends Model
{
    /** @use HasFactory<\Database\Factories\WorkerRatingFactory> */
    use HasFactory;

    protected $fillable = ['worker_id', 'total_rating', 'works_num', 'total_hours'];


    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
