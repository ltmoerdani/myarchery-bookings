<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionDistance extends Model
{
  use HasFactory;

  protected $table = 'competition_distance';
  protected $fillable = [
    'name',
    'description',
    'is_active',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];
}
