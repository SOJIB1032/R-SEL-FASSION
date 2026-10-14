<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug', 
        'sku', 
        'category_id',
        'sub_category_id', 
        'brand_id',
        'short_description',
        'description', 
        'price', 
        'discount_price',
        'stock_quantity',
        'low_stock_threshold', 
        'weight',
        'dimensions',
        'status',
        'is_featured',
        'is_new_arrival', 'is_best_seller',
        'meta_title', 
        'meta_description',
        'flash_sale_price',
        'flash_sale_starts_at',
        'flash_sale_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_best_seller' => 'boolean',
            'flash_sale_price' => 'decimal:2',
            'flash_sale_starts_at' => 'datetime',
            'flash_sale_ends_at' => 'datetime',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

   
public function isFlashSaleActive(): bool
{
    $now = now();

    return $this->flash_sale_price !== null
        && $this->flash_sale_starts_at !== null
        && $this->flash_sale_ends_at !== null
        && $this->flash_sale_starts_at->lte($now)
        && $this->flash_sale_ends_at->gte($now)
        && (float) $this->flash_sale_price < (float) $this->price
        && $this->status === 'active';
}

public function getFinalPriceAttribute(): float
{
    if ($this->isFlashSaleActive()) {
        return (float) $this->flash_sale_price;
    }

    return (float) ($this->discount_price ?? $this->price);
}


    public function getIsInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
