<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class checkin extends Model
{
    protected $table = 'checkin'; // Specify the actual table name
    protected $fillable = ['id_user', 'id_park', 'checkin','checkout','cost','name_user','name_park']; // Specify fillable fields

}
