<?php

namespace App\Models;

class Buyer extends User
{
//    use HasFactory;

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
