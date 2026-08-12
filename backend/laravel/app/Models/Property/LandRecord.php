<?php

namespace App\Models\Property;

use App\Models\System\Company;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandRecord extends Model
{
    protected $fillable = [
        'company_id', 'temple_id', 'property_id', 'record_id', 'survey_number', 'khata_number',
        'plot_number', 'village', 'tehsil', 'district', 'state', 'area_hectares', 'soil_type',
        'irrigation_type', 'crop_details', 'record_date', 'document_path', 'remarks',
    ];

    protected function casts(): array
    {
        return ['area_hectares' => 'decimal:2', 'record_date' => 'date'];
    }

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
