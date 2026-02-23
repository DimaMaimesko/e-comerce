<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'customer_id',
        'items',
        'status',
        'total_amount',
        'shipping_cost',
        'payment_transaction_id',
        'tracking_number',
        'shipping_method',
    ];

    protected $casts = [
        'items' => 'array',
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getGrandTotal(): float
    {
        return (float)$this->total_amount + (float)$this->shipping_cost;
    }

    public function getTotalWeight(): float
    {
        $weight = 0;
        foreach ($this->items as $item) {
            $weight += $item['weight'] * $item['quantity'];
        }
        return $weight;
    }

    public function isPaid(): bool
    {
        return in_array($this->status, [
            self::STATUS_PAID,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED
        ]);
    }

    public function calculateTotal(): void
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        $this->total_amount = $total;
        $this->save();
    }
}
