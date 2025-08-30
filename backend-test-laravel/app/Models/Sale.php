<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    // Relasi ke User (Sales Person)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke SalesOrder
    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }

    // Relasi ke SalesTarget
    public function salesTargets(): HasMany
    {
        return $this->hasMany(SalesTarget::class);
    }
}