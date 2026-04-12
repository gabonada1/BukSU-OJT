<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemUpdate extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'release_version',
        'release_url',
        'notes',
        'status',
        'options',
        'logs',
        'backup_path',
        'error_message',
        'started_at',
        'finished_at',
        'triggered_by',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'logs' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(CentralSuperadmin::class, 'triggered_by');
    }
}
