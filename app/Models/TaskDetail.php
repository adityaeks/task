<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskDetail extends Model
{
    protected $fillable = [
        'task_header_id',
        'name',
        'desc',
        'status',
    ];

    /**
     * A task detail belongs to one task header.
     */
    public function header(): BelongsTo
    {
        return $this->belongsTo(TaskHeader::class, 'task_header_id');
    }
}
