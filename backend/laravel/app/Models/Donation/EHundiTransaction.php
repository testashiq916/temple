<?php

namespace App\Models\Donation;

use App\Models\System\Company;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EHundiTransaction extends Model
{
    protected $table = 'e_hundi_transactions';

    protected $fillable = [
        'company_id', 'temple_id', 'transaction_id', 'qr_code', 'amount', 'payment_method',
        'upi_id', 'transaction_date', 'gateway_response', 'status', 'donation_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
            'gateway_response' => 'array',
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

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }
}
