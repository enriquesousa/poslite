<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getImageAttribute($image){
        if (file_exists('storage/categories/' . $image)) {
            return $image;
        }else{
            return 'noimg.jpg';
        }
    }
}
