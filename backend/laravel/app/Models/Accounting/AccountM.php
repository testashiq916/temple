<?php

namespace App\Models\Accounting;

use App\Models\System\Company;
use App\Models\System\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountM extends Model
{
    protected $table = 'accountm';

    protected $fillable = [
        'company_id', 'accode', 'name', 'grcode', 'bshead', 'actype', 'opening_balance',
        'status', 'is_default', 'address', 'contact_person', 'phone', 'email', 'tax_number',
        'gst_type', 'gstin', 'bank_name', 'bank_branch', 'bank_account_number', 'bank_ifsc',
        'created_by',
    ];

    protected function casts(): array
    {
        return ['opening_balance' => 'decimal:2', 'is_default' => 'boolean'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(AccountG::class, 'grcode', 'grcode');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function daybookEntries(): HasMany
    {
        return $this->hasMany(Daybook::class, 'accode', 'accode');
    }

    /**
     * Current balance = opening balance + debits - credits (for debit-natured accounts),
     * inverted for credit-natured accounts, per the double-entry convention in accountm.actype.
     */
    public function getCurrentBalanceAttribute(): float
    {
        $debit = (float) $this->daybookEntries()->where('drcr', 'dr')->sum('amount');
        $credit = (float) $this->daybookEntries()->where('drcr', 'cr')->sum('amount');

        return $this->actype === 'debit'
            ? (float) $this->opening_balance + $debit - $credit
            : (float) $this->opening_balance + $credit - $debit;
    }
}
