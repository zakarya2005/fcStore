<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adresse extends Model
{
    protected $fillable = [
        'adresse', 'city', 'postal_code', 'region'
    ];
}
