<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'sales_id',
        'customer_id'
    ];

    // Relasi ke Sales
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sales_id');
    }

    // Relasi ke Customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi ke item-item dalam order
    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class, 'order_id');
    }
}