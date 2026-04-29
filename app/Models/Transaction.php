<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'order_id', 'amount', 'platform_cut',
        'farmer_payout', 'payment_reference', 'method', 'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_cut' => 'decimal:2',
        'farmer_payout' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}