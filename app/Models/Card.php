<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
