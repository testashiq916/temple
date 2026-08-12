<?php

namespace App\Models\Property;

use App\Models\System\Company;
use App\Models\System\User;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TempleProperty extends Model
{
    protected $fillable = [
        'company_id', 'temple_id', 'property_id', 'name', 'property_type', 'description',
        'address', 'city', 'state', 'country', 'zip_code', 'latitude', 'longitude',
        'total_area', 'area_unit', 'purchase_date', 'purchase_price', 'current_value',
        'valuation_date', 'title_deed_number', 'survey_number', 'document_path', 'status',
        'ownership_type', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'valuation_date' => 'date',
            'purchase_price' => 'decimal:2',
            'current_value' => 'decimal:2',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function landRecords(): HasMany
    {
        return $this->hasMany(LandRecord::class, 'property_id');
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(PropertyTenant::class, 'property_id');
    }
}
