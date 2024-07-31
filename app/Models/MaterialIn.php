<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialIn extends Model
{
    use HasFactory;

    protected $table = 'material_ins';

    protected $fillable = [
        'date',
        'material_id',
        'qty'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
