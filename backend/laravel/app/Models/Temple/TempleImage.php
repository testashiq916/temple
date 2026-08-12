<?php

namespace App\Models\Temple;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleImage extends Model
{
    public $timestamps = false;

    protected $fillable = ['temple_id', 'image_path', 'image_name', 'image_type', 'is_primary', 'sort_order'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean', 'created_at' => 'datetime'];
    }

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }
}
