<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'coordinates',
        'area_size'
    ];
    
    // Relationship: 1 location có nhiều trees
    public function trees()
    {
        return $this->hasMany(Tree::class, 'location_id');
    }
}