<?php

namespace App\Models\Inventory;

use App\Models\System\Company;
use App\Models\System\User;
use App\Models\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'company_id', 'temple_id', 'category_id', 'item_code', 'name', 'description', 'unit',
        'quantity', 'min_quantity', 'max_quantity', 'unit_price', 'total_value', 'location',
        'expiry_date', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_value' => 'decimal:2',
            'expiry_date' => 'date',
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
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'item_id');
    }
}
