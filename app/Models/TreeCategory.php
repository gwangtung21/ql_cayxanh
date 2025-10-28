<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreeCategory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description', 
        'care_frequency_days'
    ];
    
    // Relationship: 1 category có nhiều trees
    public function trees()
    {
        return $this->hasMany(Tree::class, 'category_id');
    }
}