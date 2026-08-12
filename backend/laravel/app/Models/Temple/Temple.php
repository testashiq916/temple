<?php

namespace App\Models\Temple;

use App\Models\Crowd\CctvCamera;
use App\Models\Crowd\DarshanQueue;
use App\Models\Devotee\Devotee;
use App\Models\Donation\Donor;
use App\Models\Inventory\InventoryItem;
use App\Models\Property\TempleProperty;
use App\Models\Seva\SevaService;
use App\Models\Staff\Staff;
use App\Models\System\Company;
use App\Models\System\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Temple extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'temple_id', 'name', 'sanskrit_name', 'description', 'history',
        'address', 'city', 'state', 'country', 'zip_code', 'latitude', 'longitude',
        'phone', 'email', 'website', 'established_year', 'deity_name', 'deity_description',
        'temple_type', 'capacity', 'is_active', 'profile_image', 'banner_image', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(TempleImage::class);
    }

    public function deities(): HasMany
    {
        return $this->hasMany(Deity::class);
    }

    public function festivals(): HasMany
    {
        return $this->hasMany(Festival::class);
    }

    public function devotees(): HasMany
    {
        return $this->hasMany(Devotee::class);
    }

    public function donors(): HasMany
    {
        return $this->hasMany(Donor::class);
    }

    public function sevaServices(): HasMany
    {
        return $this->hasMany(SevaService::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(TempleProperty::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function cctvCameras(): HasMany
    {
        return $this->hasMany(CctvCamera::class);
    }

    public function darshanQueues(): HasMany
    {
        return $this->hasMany(DarshanQueue::class);
    }
}
