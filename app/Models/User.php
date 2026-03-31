<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'      => 'datetime',
            'password'               => 'hashed',
            'two_factor_confirmed_at'=> 'datetime',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function clientProfile()
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    public function clientProjects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    public function memberProjects()
    {
        return $this->belongsToMany(Project::class, 'project_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function workSessions()
    {
        return $this->hasMany(WorkSession::class);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role?->name, $roles);
    }
}