<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'task_id', 'submitted_by', 'file_path', 'comment',
        'pm_note', 'pm_to_client_comment', 'pm_to_client_file',
        'status', 'client_submitted', 'client_status', 'client_note',
    ];

    protected $casts = [
        'client_submitted' => 'boolean',
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