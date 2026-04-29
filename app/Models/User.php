<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'address',
        'farm_name', 'farmer_id_path', 'is_approved',
        'latitude', 'longitude', 'bio', 'two_factor_secret', 'two_factor_confirmed',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    // Role helpers
    public function isFarmer(): bool { return $this->role === 'farmer'; }
    public function isBuyer(): bool  { return $this->role === 'buyer'; }
    public function isAdmin(): bool  { return $this->role === 'admin'; }

    // Farmer relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function farmerOrders()
    {
        return $this->hasMany(Order::class, 'farmer_id');
    }

    // Buyer relationships
    public function buyerOrders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function customTags()
    {
        return $this->hasMany(Tag::class);
    }

    public function socialLinks()
    {
        return $this->hasMany(FarmerSocialLink::class);
    }
}