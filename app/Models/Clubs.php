<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clubs extends Model
{
  use HasFactory;

  protected $table = 'clubs';
  protected $fillable = [
    'name',
    'logo',
    'banner',
    'place_name',
    'country_id',
    'country_name',
    'state_id',
    'state_name',
    'city_id',
    'city_name',
    'address',
    'description',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];
}
