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
        'status'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }
}
