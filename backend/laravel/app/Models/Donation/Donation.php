<?php

namespace App\Models\Donation;

use App\Models\Accounting\Voucher;
use App\Models\Devotee\Devotee;
use App\Models\System\Company;
use App\Models\System\User;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'temple_id', 'donor_id', 'devotee_id', 'category_id', 'donation_id',
        'amount', 'donation_date', 'donation_type', 'payment_method', 'transaction_id',
        'is_anonymous', 'is_recurring', 'recurrence_pattern', 'purpose', 'notes', 'status',
        'receipt_id', 'voucher_id', 'verified_by', 'verified_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'donation_date' => 'date',
            'is_anonymous' => 'boolean',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'array',
            'verified_at' => 'datetime',
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

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function devotee(): BelongsTo
    {
        return $this->belongsTo(Devotee::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DonationCategory::class, 'category_id');
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function eHundiTransaction(): HasOne
    {
        return $this->hasOne(EHundiTransaction::class);
    }
}
