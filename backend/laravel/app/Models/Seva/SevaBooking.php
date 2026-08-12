<?php

namespace App\Models\Seva;

use App\Models\Accounting\Voucher;
use App\Models\Devotee\Devotee;
use App\Models\System\Company;
use App\Models\System\User;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SevaBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'temple_id', 'booking_id', 'devotee_id', 'seva_id', 'slot_id',
        'booking_date', 'slot_date', 'start_time', 'end_time', 'number_of_devotees',
        'devotee_names', 'special_requests', 'total_amount', 'discount_amount', 'tax_amount',
        'net_amount', 'status', 'payment_status', 'receipt_id', 'voucher_id', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'slot_date' => 'date',
            'devotee_names' => 'array',
            'total_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
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

    public function seva(): BelongsTo
    {
        return $this->belongsTo(SevaService::class, 'seva_id');
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(SevaSlot::class, 'slot_id');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
