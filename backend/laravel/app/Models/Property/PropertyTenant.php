<?php

namespace App\Models\Property;

use App\Models\System\Company;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyTenant extends Model
{
    protected $fillable = [
        'company_id', 'temple_id', 'property_id', 'tenant_id', 'full_name', 'contact_person',
        'mobile', 'email', 'address', 'city', 'state', 'country', 'postal_code',
        'business_type', 'business_license_number', 'status',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(TempleProperty::class, 'property_id');
    }
}
