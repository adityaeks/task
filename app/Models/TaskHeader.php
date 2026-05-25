<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskHeader extends Model
{
    protected $fillable = [
        'title',
        'path',
        'date',
        'note',
        'user',
        'category',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * A task header has many detail items.
     */
    public function details(): HasMany
    {
        return $this->hasMany(TaskDetail::class, 'task_header_id');
    }
}
