<?php

namespace App\Models\Donation;

use App\Models\Devotee\Devotee;
use App\Models\System\Company;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'temple_id', 'donor_id', 'devotee_id', 'full_name', 'sanskrit_name',
        'email', 'mobile', 'address', 'city', 'state', 'country', 'postal_code',
        'donor_type', 'pan_card', 'tax_exempt', 'is_active',
    ];

    protected function casts(): array
    {
        return ['tax_exempt' => 'boolean', 'is_active' => 'boolean'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function devotee(): BelongsTo
    {
        return $this->belongsTo(Devotee::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}
