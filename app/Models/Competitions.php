<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competitions extends Model
{
  use HasFactory;

  protected $table = 'competitions';
  protected $fillable = [
    'name',
    'event_id',
    'competition_type_id',
    'competition_category_id',
    'gender',
    'contingent',
    'distance',
    'class_type',
    'class_name',
    'description',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];
}
