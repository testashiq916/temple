<?php

namespace App\Models\Temple;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Festival extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'temple_id', 'festival_id', 'name', 'sanskrit_name', 'description',
        'significance', 'festival_type', 'start_date', 'end_date', 'hijri_date',
        'is_recurring', 'recurrence_pattern', 'image_path', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'array',
            'is_active' => 'boolean',
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
