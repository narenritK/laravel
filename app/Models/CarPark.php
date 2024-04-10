<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarPark extends Model
{
    protected $table = 'car_park'; // Specify the actual table name
    protected $fillable = ['name', 'latitude', 'longitude','slot','cost']; // Specify fillable fields

    // Define any relationships or additional methods here
}
