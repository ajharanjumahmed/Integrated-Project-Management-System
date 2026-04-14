<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'project_id',
        'sender_id',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d\TH:i:s\Z');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
