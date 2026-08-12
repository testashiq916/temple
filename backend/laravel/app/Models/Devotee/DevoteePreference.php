<?php

namespace App\Models\Devotee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevoteePreference extends Model
{
    protected $fillable = ['devotee_id', 'preference_type', 'preference_value'];

    public function devotee(): BelongsTo
    {
        return $this->belongsTo(Devotee::class);
    }
}
