<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory;

    protected $appends = ['is_wishlist'];

    protected $fillable = [
        'name',
        'description',
        'price',
        'categories_id',
        'tags',
    ];

    public function getIsWishlistAttribute()
    {
        $wishlist = WishlistItem::with(['wishlist'])->where('product_id', $this->id)->first();
        if (!isset($wishlist->wishlist)) {
            return false;
        }
        if ($wishlist->wishlist->user_id == Auth::user()->id && $wishlist->count() > 0) {
            return true;
        }
        return false;
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'product_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
