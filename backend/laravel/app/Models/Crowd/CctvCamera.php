<?php

namespace App\Models\Crowd;

use App\Models\System\Company;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CctvCamera extends Model
{
    protected $table = 'cctv_cameras';

    protected $fillable = [
        'company_id', 'temple_id', 'camera_id', 'name', 'location', 'camera_type',
        'rtsp_url', 'ai_enabled', 'model_config', 'is_active',
    ];

    protected function casts(): array
    {
        return ['ai_enabled' => 'boolean', 'model_config' => 'array', 'is_active' => 'boolean'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(AiDetectionAlert::class, 'camera_id');
    }
}
