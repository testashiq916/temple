<?php

namespace App\Models\Crowd;

use App\Models\System\Company;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrowdAnalytics extends Model
{
    protected $table = 'crowd_analytics';

    public $timestamps = false;

    protected $fillable = [
        'company_id', 'temple_id', 'analytics_date', 'hour', 'total_devotees', 'predicted_devotees',
        'peak_capacity_percent', 'average_darshan_time', 'average_queue_length', 'congestion_level',
        'heatmap_data',
    ];

    protected function casts(): array
    {
        return [
            'analytics_date' => 'date',
            'peak_capacity_percent' => 'decimal:2',
            'heatmap_data' => 'array',
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
}
