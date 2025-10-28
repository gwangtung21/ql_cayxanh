<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareSchedule extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tree_id',
        'care_type',
        'frequency_days',
        'next_due_date',
        'assigned_to',
        'notes',
        'is_active'
    ];
    
    protected $casts = [
        'next_due_date' => 'date',
        'is_active' => 'boolean'
    ];
    
    // Relationship: CareSchedule thuộc về 1 tree
    public function tree()
    {
        return $this->belongsTo(Tree::class);
    }
    
    // Relationship: CareSchedule được assign cho 1 user
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}