<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
  use HasFactory;

  protected $table = 'currency';
  protected $fillable = [
    'name',
    'symbol',
    'symbol_position',
    'text',
    'text_position',
    'rate',
    'description',
    'is_active',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];
}
