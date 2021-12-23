<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'barcode',
        'cost',
        'price',
        'stock',
        'alerts',
        'image',
        'category_id'
    ];

    public function category()
    {
        return $this->belongTo(Category::class);
    }

    public function getImageAttribute($image){

        // Segun otra forma de hacerlo, no me salio a mi 
        // if($this->image != null)
        //     return (file_exists('storage/products/' . $this->image) ? $this->image : 'noimg.jpg');
        // else
        //     return 'noimg.jpg';

        // Otra forma de hacerlo seria, esta si me funciono a mi:
        if (file_exists('storage/products/' . $image)) {
            return $image;
        }else{
            return 'noimg.jpg';
        }

    }

}
