<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternationalCountries extends Model
{
  use HasFactory;


  protected $table = 'international_countries';
  protected $fillable = [
    'id',
    'name',
    'iso3',
    'iso2',
    'numeric_code',
    'phone_code',
    'capital',
    'currency',
    'currency_name',
    'currency_symbol',
    'tld',
    'native',
    'region',
    'region_id',
    'sub_region',
    'sub_region_id',
    'nationality',
    'timezones',
    'latitude',
    'longitude',
    'emoji',
    'emojiU',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];

  public function internationalStates()
  {
    return $this->hashMany(InternationalStates::class);
  }

  public function internationalCities()
  {
    return $this->hashMany(InternationalCities::class);
  }
}
