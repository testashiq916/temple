<?php

namespace App\Models\Temple;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deity extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'temple_id', 'deity_id', 'name', 'sanskrit_name', 'description',
        'avatar', 'consort_name', 'vehicle', 'color', 'mantra', 'significance',
        'image_path', 'is_primary', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean', 'is_active' => 'boolean'];
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
