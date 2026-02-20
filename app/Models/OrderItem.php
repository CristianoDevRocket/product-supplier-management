<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted(): void
    {
        static::creating(function (OrderItem $item) {
            $item->total_price = $item->quantity * $item->unit_price;
        });

        static::updating(function (OrderItem $item) {
            $item->total_price = $item->quantity * $item->unit_price;
        });

        static::saved(function (OrderItem $item) {
            $item->order->recalculateTotal();
        });

        static::deleted(function (OrderItem $item) {
            $item->order->recalculateTotal();
        });
    }
}
