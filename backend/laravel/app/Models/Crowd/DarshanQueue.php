<?php

namespace App\Models\Crowd;

use App\Models\System\Company;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DarshanQueue extends Model
{
    protected $fillable = [
        'company_id', 'temple_id', 'queue_id', 'queue_type', 'start_time', 'estimated_wait_time',
        'actual_wait_time', 'total_devotees', 'current_position', 'status',
    ];

    protected function casts(): array
    {
        return ['start_time' => 'datetime'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(QueueEntry::class, 'queue_id');
    }
}
