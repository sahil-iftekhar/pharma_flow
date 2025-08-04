<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'dosage',
        'brand',
        'image_url',
        'stock',
    ];

    /**
     * The categories that belong to the medicine.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'medicine_categories');
    }

    /**
     * Get the cart items for the medicine.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
