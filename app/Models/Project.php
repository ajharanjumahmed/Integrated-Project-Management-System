<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'client_id',
        'project_manager_id',
        'budget',
        'start_date',
        'deadline',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline'   => 'date',
        'budget'     => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function projectMembers()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class)->orderBy('deadline');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function workSessions()
    {
        return $this->hasMany(WorkSession::class);
    }
}