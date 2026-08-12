<?php

namespace App\Models\Crowd;

use App\Models\Devotee\Devotee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueueEntry extends Model
{
    protected $fillable = [
        'queue_id', 'devotee_id', 'queue_number', 'entry_time', 'exit_time', 'status',
        'qr_code', 'wristband_id', 'darshan_duration',
    ];

    protected function casts(): array
    {
        return ['entry_time' => 'datetime', 'exit_time' => 'datetime'];
    }

    public function queue(): BelongsTo
    {
        return $this->belongsTo(DarshanQueue::class, 'queue_id');
    }

    public function devotee(): BelongsTo
    {
        return $this->belongsTo(Devotee::class);
    }
}
