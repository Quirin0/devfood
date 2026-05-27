<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'logo',
        'category_label',
        'category_id',
        'rating',
        'reviews_count',
        'delivery_time',
        'delivery_fee',
        'min_order',
        'is_free_delivery',
        'is_featured',
        'is_open',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'delivery_fee' => 'decimal:2',
            'min_order' => 'decimal:2',
            'is_free_delivery' => 'boolean',
            'is_featured' => 'boolean',
            'is_open' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->orderBy('sort_order');
    }
}
