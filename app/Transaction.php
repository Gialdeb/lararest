<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    // database
    protected  $fillable = [
        'quantity',
        'buyer_id',
        'product_id',
    ];

    public function buyer(){

        return $this->belongsTo(Buyer::class);
    }

    public function product(){

        return $this->belongsTo(Product::class);
    }
}
