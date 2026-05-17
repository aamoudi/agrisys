<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harvest extends Model
{
    /** @use HasFactory<\Database\Factories\HarvestFactory> */
    use HasFactory;

    protected $fillable = [
        'crop_id',
        'quantitiy',
        'unit',
        'harvest_date',
        'revenue'
    ];

    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }
}
