<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    /** @use HasFactory<\Database\Factories\FarmFactory> */
    use HasFactory;

    protected $fillable = ['name', 'city_id', 'user_id', 'soil_type_cd', 'size_hectares', 'notes'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function crops()
    {
        return $this->hasMany(Crop::class);
    }

    /**
     * Relationship: A Farm has a Soil Type (from lookup_children)
     */
    public function soilType()
    {
        return $this->belongsTo(Lookup_child::class, 'soil_type_cd', 'id');
    }

    public function workers()
    {
        return $this->hasMany(WorkDetail::class);
    }
}
