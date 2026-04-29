<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'buyer_id', 'farmer_id', 'status', 'delivery_address',
        'delivery_scheduled_at', 'payment_method', 'payment_status',
        'subtotal', 'delivery_fee', 'total', 'notes',
    ];

    protected $casts = [
        'delivery_scheduled_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    // Scopes
    public function scopeForBuyer($query, $userId)
    {
        return $query->where('buyer_id', $userId);
    }

    public function scopeForFarmer($query, $userId)
    {
        return $query->where('farmer_id', $userId);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}