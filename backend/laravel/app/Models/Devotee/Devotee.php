<?php

namespace App\Models\Devotee;

use App\Models\Donation\Donation;
use App\Models\Donation\Donor;
use App\Models\Seva\SevaBooking;
use App\Models\Staff\Volunteer;
use App\Models\System\Company;
use App\Models\System\User;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devotee extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'temple_id', 'devotee_id', 'user_id', 'devotee_type_id',
        'first_name', 'last_name', 'sanskrit_name', 'gender', 'date_of_birth',
        'place_of_birth', 'gotra', 'rashi', 'nakshatra', 'nationality', 'email',
        'mobile', 'alternate_mobile', 'address', 'city', 'state', 'country', 'zip_code',
        'occupation', 'employer', 'profile_image', 'family_members', 'is_member',
        'membership_type', 'membership_start_date', 'membership_end_date', 'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'membership_start_date' => 'date',
            'membership_end_date' => 'date',
            'is_member' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
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

    public function devoteeType(): BelongsTo
    {
        return $this->belongsTo(DevoteeType::class);
    }

    public function family(): HasMany
    {
        return $this->hasMany(DevoteeFamily::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DevoteeDocument::class);
    }

    public function preferences(): HasMany
    {
        return $this->hasMany(DevoteePreference::class);
    }

    public function sevaBookings(): HasMany
    {
        return $this->hasMany(SevaBooking::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function donorProfile(): HasMany
    {
        return $this->hasMany(Donor::class);
    }

    public function volunteerProfile(): HasMany
    {
        return $this->hasMany(Volunteer::class);
    }
}
