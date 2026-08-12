<?php

namespace App\Models\Accounting;

use App\Models\Devotee\Devotee;
use App\Models\Donation\Donation;
use App\Models\Seva\SevaBooking;
use App\Models\System\Company;
use App\Models\System\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Daybook extends Model
{
    protected $table = 'daybook';

    protected $fillable = [
        'company_id', 'sno', 'accode', 'opaccode', 'amount', 'drcr', 'voucher_type',
        'voucher_no', 'voucher_date', 'remarks', 'reference_no', 'reference_date',
        'devotee_id', 'seva_booking_id', 'donation_id', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'voucher_date' => 'date',
            'reference_date' => 'date',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(AccountM::class, 'accode', 'accode');
    }

    public function oppositeAccount(): BelongsTo
    {
        return $this->belongsTo(AccountM::class, 'opaccode', 'accode');
    }

    public function devotee(): BelongsTo
    {
        return $this->belongsTo(Devotee::class);
    }

    public function sevaBooking(): BelongsTo
    {
        return $this->belongsTo(SevaBooking::class);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
