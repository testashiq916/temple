<?php

namespace App\Models\Crowd;

use App\Models\System\Company;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiDetectionAlert extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company_id', 'temple_id', 'camera_id', 'alert_type', 'severity', 'description',
        'detection_data', 'is_resolved', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'detection_data' => 'array',
            'is_resolved' => 'boolean',
            'resolved_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function camera(): BelongsTo
    {
        return $this->belongsTo(CctvCamera::class, 'camera_id');
    }
}
