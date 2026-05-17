<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lookup_parent extends Model
{
    /** @use HasFactory<\Database\Factories\LookupParentFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    public function lookup_children(){
        return $this->hasMany(Lookup_child::class);
    }
}
