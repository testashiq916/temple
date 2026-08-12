<?php

namespace App\Models\Seva;

use App\Models\System\Company;
use App\Models\System\User;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SevaService extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'temple_id', 'category_id', 'seva_id', 'name', 'sanskrit_name',
        'description', 'procedure', 'duration_minutes', 'price', 'gst_rate', 'discount_percent',
        'max_devotees', 'min_advance_days', 'max_advance_days', 'is_active', 'requires_approval',
        'image_path', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'requires_approval' => 'boolean',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(SevaCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function slots(): HasMany
    {
        return $this->hasMany(SevaSlot::class, 'seva_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(SevaBooking::class, 'seva_id');
    }
}
