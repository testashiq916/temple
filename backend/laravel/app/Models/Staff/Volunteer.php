<?php

namespace App\Models\Staff;

use App\Models\Devotee\Devotee;
use App\Models\System\Company;
use App\Models\System\User;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Volunteer extends Model
{
    protected $fillable = [
        'company_id', 'temple_id', 'volunteer_id', 'devotee_id', 'first_name', 'last_name',
        'email', 'mobile', 'address', 'city', 'state', 'country', 'postal_code', 'skills',
        'availability', 'status', 'join_date', 'total_hours', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'availability' => 'array',
            'join_date' => 'date',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
