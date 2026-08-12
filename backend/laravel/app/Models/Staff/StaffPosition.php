<?php

namespace App\Models\Staff;

use App\Models\System\Company;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffPosition extends Model
{
    protected $fillable = [
        'company_id', 'temple_id', 'name', 'sanskrit_name', 'description',
        'salary_range_min', 'salary_range_max', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'salary_range_min' => 'decimal:2',
            'salary_range_max' => 'decimal:2',
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

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class, 'position_id');
    }
}
