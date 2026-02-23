<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock',
        'weight',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function decreaseStock(int $quantity): void
    {
        if ($quantity > $this->stock) {
            throw new \DomainException(
                "Insufficient stock for product: {$this->name}"
            );
        }

        $this->stock -= $quantity;
        $this->save();
    }

    public function increaseStock(int $quantity): void
    {
        $this->stock += $quantity;
        $this->save();
    }

    public function isInStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }
}
