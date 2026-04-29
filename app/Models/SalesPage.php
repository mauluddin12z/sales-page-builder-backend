<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_name',
        'description',
        'features',
        'target_audience',
        'price',
        'usp',
        'generated_content',
        'template',
        'cta_label',
        'cta_url',
    ];

    protected $casts = [
        'features' => 'array',
    ];
}
