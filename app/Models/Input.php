<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Input extends Model
{

    /** @use HasFactory<\Database\Factories\FarmFactory> */
    use HasFactory;

    protected $fillable = [
        'crop_id',
        'type_cd',
        'quantity',
        'cost',
        'applied_at'
    ];

    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }
}
