<?php

namespace App\Models\Accounting;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountG extends Model
{
    protected $table = 'accountg';

    protected $fillable = ['company_id', 'grcode', 'grname', 'bshead_id', 'parent_grcode', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function balanceSheetHead(): BelongsTo
    {
        return $this->belongsTo(AccountGBS::class, 'bshead_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AccountG::class, 'parent_grcode', 'grcode');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(AccountM::class, 'grcode', 'grcode');
    }
}
