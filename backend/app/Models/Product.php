<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'image_path', 'description', 'price', 'discount', 'category',
    ];

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

}
