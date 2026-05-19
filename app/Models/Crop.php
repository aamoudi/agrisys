<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    /** @use HasFactory<\Database\Factories\CropFactory> */
    use HasFactory;

    protected $fillable = ['name', 'farm_id', 'variety_cd', 'planting_date',
             'type', 'expected_harvest_date', 'status', 'notes'];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function harvests()
    {
        return $this->hasMany(Harvest::class);
    }

    public function inputs()
    {
        return $this->hasMany(Input::class);
    }

    public function variety()
    {
        return $this->belongsTo(Lookup_child::class, 'variety_cd', 'id');
    }

}
