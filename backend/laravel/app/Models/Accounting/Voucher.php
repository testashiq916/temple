<?php

namespace App\Models\Accounting;

use App\Models\System\Company;
use App\Models\System\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    protected $fillable = [
        'company_id', 'voucher_no', 'voucher_type', 'voucher_date', 'reference_no',
        'reference_date', 'narration', 'total_amount', 'is_posted', 'posted_by',
        'posted_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'voucher_date' => 'date',
            'reference_date' => 'date',
            'total_amount' => 'decimal:2',
            'is_posted' => 'boolean',
            'posted_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Daybook::class, 'voucher_no', 'voucher_no');
    }
}
