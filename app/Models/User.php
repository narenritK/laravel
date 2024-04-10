<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['name', 'lat', 'long']; // Specify fillable fields

    // Define any relationships or additional methods here
}