<?php

namespace App\Models\Donation;

use App\Models\Devotee\Devotee;
use App\Models\System\Company;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalGoldTransaction extends Model
{
    protected $fillable = [
        'company_id', 'temple_id', 'transaction_id', 'devotee_id', 'donor_id', 'amount',
        'gold_weight_grams', 'gold_purity', 'transaction_date', 'payment_method',
        'gateway_response', 'status', 'redemption_date', 'redeem_to',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'gold_weight_grams' => 'decimal:3',
            'transaction_date' => 'date',
            'gateway_response' => 'array',
            'redemption_date' => 'date',
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

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }
}
