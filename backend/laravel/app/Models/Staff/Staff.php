<?php

namespace App\Models\Staff;

use App\Models\System\Company;
use App\Models\System\User;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'company_id', 'temple_id', 'staff_id', 'user_id', 'position_id', 'first_name',
        'last_name', 'sanskrit_name', 'gender', 'date_of_birth', 'email', 'mobile', 'address',
        'city', 'state', 'country', 'postal_code', 'qualification', 'experience_years',
        'profile_image', 'joining_date', 'employee_type', 'basic_salary', 'allowances',
        'status', 'bank_name', 'bank_account_number', 'bank_ifsc', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'joining_date' => 'date',
            'basic_salary' => 'decimal:2',
            'allowances' => 'array',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(StaffPosition::class, 'position_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
