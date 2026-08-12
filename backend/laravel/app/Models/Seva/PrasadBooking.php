<?php

namespace App\Models\Seva;

use App\Models\Devotee\Devotee;
use App\Models\System\Company;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrasadBooking extends Model
{
    protected $fillable = [
        'company_id', 'temple_id', 'booking_id', 'devotee_id', 'prasad_type', 'quantity',
        'unit_price', 'total_amount', 'booking_date', 'collection_date', 'collection_time',
        'status', 'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'collection_date' => 'date',
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
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

    public function devotee(): BelongsTo
    {
        return $this->belongsTo(Devotee::class);
    }
}
