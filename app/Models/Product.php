<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Tag;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\OrderItem;
use App\Models\User;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description',
        'price', 'unit', 'stock', 'minimum_order', 'pickup_location',
        'status', 'is_available', 'is_priority_listed', 'harvest_date',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_priority_listed' => 'boolean',
        'harvest_date' => 'date',
        'price' => 'decimal:2',
    ];

    // Auto-generate slug from title
    protected static function booted(): void
    {
        static::creating(function ($product) {
            $product->slug = Str::slug($product->title) . '-' . Str::lower(Str::random(5));
        });
    }

    // Relationships
    public function farmer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('status', '!=', 'out_of_stock');
    }

    public function scopeByCategory($query, $categorySlug)
    {
        return $query->whereHas('category', fn($q) => $q->where('slug', $categorySlug));
    }

    // public function scopeSearch($query, $search)
    // {
    //     return $query->where('title', 'like', "%{$search}%")
    //                  ->orWhere('description', 'like', "%{$search}%");
    // }

    // Helpers
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= 10;
    }

    public function scopeSearch($query, $search)
    {
        $query->where(function ($q) use ($search) {

            $q->where('title', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")

            ->orWhereHas('farmer', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                    ->orWhere('farm_name', 'like', "%{$search}%");
            });

        });
    }
}