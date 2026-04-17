<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'discount', 
        'category_id', 'brand', 'stock_quantity', 'image', 'is_active'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
