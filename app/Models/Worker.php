<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    /** @use HasFactory<\Database\Factories\WorkerFactory> */
    use HasFactory;

    protected $fillable = ['name', 'phone_number', 'pic', 'job_cd', 'notes'];

    public function job()
    {
        return $this->belongsTo(Lookup_child::class, 'job_cd', 'id');
    }

    public function workDetails()
    {
        return $this->hasMany(Work_Detail::class);
    }

    public function ratingCache()
    {
        return $this->hasOne(Worker_Rating::class);
    }
}
