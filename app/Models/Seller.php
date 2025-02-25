<?php

namespace App\Models;


class Seller extends User
{
//    use HasFactory;
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

