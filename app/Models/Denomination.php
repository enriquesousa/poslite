<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denomination extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'value',
        'image'
    ];

    
    // public function getImageAttribute($image){
    //     if (file_exists('storage/denominations/' . $image)) {
    //         return $image;
    //     }else{
    //         return 'noimg.jpg';
    //     }
    // }

    // Esto si me funciono! encontre la funcion is_null en internet
    public function getImageAttribute($image)
    {
        if (!is_null($image)) {
            return $image;
        } else {
            return 'noimg.jpg';
        }
    }

}
