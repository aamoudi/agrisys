<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lookup_child extends Model
{
    /** @use HasFactory<\Database\Factories\LookupChildFactory> */
    use HasFactory;

    protected $fillable = ['lookup_parent_id', 'name'];

    public function lookup_parent(){
        return $this->belongsTo(Lookup_parent::class);
    }
}
