<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'qty'
    ];

    public function product_material()
    {
        return $this->hasMany(ProductMaterial::class, 'product_id');
    }

}
