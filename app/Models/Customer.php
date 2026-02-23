<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'addresses',
    ];

    protected $casts = [
        'addresses' => 'array',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addAddress(string $address): void
    {
        $addresses = $this->addresses ?? [];
        $addresses[] = $address;
        $this->addresses = $addresses;
        $this->save();
    }
}
