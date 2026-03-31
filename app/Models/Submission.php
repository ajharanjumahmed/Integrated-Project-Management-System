<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'task_id',
        'submitted_by',
        'file_path',
        'comment',
        'status',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}