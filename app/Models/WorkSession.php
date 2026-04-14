<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSession extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'start_time', 'end_time', 'duration',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d\TH:i:s\Z');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('end_time');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}