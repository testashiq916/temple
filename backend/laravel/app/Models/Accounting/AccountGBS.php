<?php

namespace App\Models\Accounting;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountGBS extends Model
{
    protected $table = 'accountgbs';

    protected $fillable = ['company_id', 'bshead', 'bshead_name', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(AccountG::class, 'bshead_id');
    }
}
