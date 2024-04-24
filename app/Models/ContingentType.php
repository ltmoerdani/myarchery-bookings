<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContingentType extends Model
{
  use HasFactory;

  protected $table = 'contingent_type';
  protected $fillable = [
    'event_id',
    'contingent_type',
    'select_type',
    'country_id',
    'country',
    'province_id',
    'province',
    'state_id',
    'state',
    'city_id',
    'city',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];
}
