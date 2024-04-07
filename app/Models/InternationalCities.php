<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternationalCities extends Model
{
  use HasFactory;

  protected $table = 'international_cities';
  protected $fillable = [
    'id',
    'name',
    'state_id',
    'state_code',
    'state_name',
    'country_id',
    'country_code',
    'country_name',
    'state_code',
    'latitude',
    'longitude',
    'wikiDataId',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];

  public function internationalCountries()
  {
    return $this->belongsTo(InternationalCountries::class);
  }

  public function internationalState()
  {
    return $this->belongsTo(InternationalStates::class);
  }
}
