<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    
    protected $fillable = [
        'project_id',
        'milestone_id',
        'assigned_to',
        'title',
        'description',
        'priority',
        'status',
        'deadline',
    ];

   
    protected $casts = [
        'deadline' => 'date',
    ];

    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    
    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}