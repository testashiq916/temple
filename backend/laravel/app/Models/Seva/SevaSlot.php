<?php

namespace App\Models\Seva;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SevaSlot extends Model
{
    protected $fillable = ['seva_id', 'slot_date', 'start_time', 'end_time', 'capacity', 'booked_count', 'is_available'];

    protected function casts(): array
    {
        return ['slot_date' => 'date', 'is_available' => 'boolean'];
    }

    public function seva(): BelongsTo
    {
        return $this->belongsTo(SevaService::class, 'seva_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(SevaBooking::class, 'slot_id');
    }

    public function getIsFullAttribute(): bool
    {
        return $this->booked_count >= $this->capacity;
    }
}
