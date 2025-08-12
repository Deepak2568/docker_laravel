<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    protected $fillable = ['name', 'status', 'categories', 'type', 'image'];
    protected $casts = [
        'categories' => 'array',
    ];
}
