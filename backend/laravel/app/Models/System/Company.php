<?php

namespace App\Models\System;

use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'name', 'code', 'email', 'phone', 'address', 'city', 'state',
        'country', 'zip_code', 'timezone', 'currency', 'date_format', 'logo_path',
        'subscription_id', 'subscription_status', 'subscription_start_date',
        'subscription_end_date', 'user_limit', 'devotee_limit', 'storage_limit', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'subscription_start_date' => 'date',
            'subscription_end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Company $company) {
            $company->uuid = $company->uuid ?? (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function temples(): HasMany
    {
        return $this->hasMany(Temple::class);
    }
}
