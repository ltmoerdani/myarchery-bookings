<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternationalStates extends Model
{
  use HasFactory;

  protected $table = 'international_states';

  protected $fillable = [
    'id',
    'name',
    'country_id',
    'country_code',
    'country_name',
    'state_code',
    'type',
    'latitude',
    'longitude',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];

  public function internationalCountries()
  {
    return $this->belongsTo(InternationalCountries::class);
  }
}
