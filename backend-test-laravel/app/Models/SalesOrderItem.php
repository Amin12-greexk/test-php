<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'production_price',
        'selling_price',
    ];

    // Relasi kembali ke SalesOrder
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }

    // Relasi ke Product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}