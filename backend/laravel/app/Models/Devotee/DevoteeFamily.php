<?php

namespace App\Models\Devotee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevoteeFamily extends Model
{
    protected $table = 'devotee_family';

    protected $fillable = [
        'devotee_id', 'full_name', 'sanskrit_name', 'relationship', 'date_of_birth',
        'gotra', 'rashi', 'nakshatra', 'gender', 'is_active',
    ];

    protected function casts(): array
    {
        return ['date_of_birth' => 'date', 'is_active' => 'boolean'];
    }

    public function devotee(): BelongsTo
    {
        return $this->belongsTo(Devotee::class);
    }
}
