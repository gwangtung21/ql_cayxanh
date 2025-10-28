<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'scientific_name',
        'category_id',
        'location_id',
        'planting_date',
        'height',
        'diameter',
        'health_status',
        'image_url',
        'notes'
    ];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'planting_date' => 'date',
        'height' => 'float',
        'diameter' => 'float',
    ];
    
    // Relationship: Tree thuộc về 1 category
    public function category()
    {
        return $this->belongsTo(TreeCategory::class);
    }
    
    // Relationship: Tree thuộc về 1 location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    
    // Relationship: Tree có nhiều care schedules
    public function careSchedules()
    {
        return $this->hasMany(CareSchedule::class);
    }
}