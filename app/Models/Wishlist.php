<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wishlist extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
    ];


    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class, 'wishlist_id', 'id');
    }
}
