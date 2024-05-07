<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Exceptions\HttpResponseException;
use Carbon\Carbon;

class HelperEvent
{

  public static function AutoGenerateCode()
  {
    try {
      $now = Carbon::now();
      $archery_words = ["Arrow", "Bow", "Quiver", "Archery", "Compound", "Recurve", "Longbow", "Crossbow", "Nasional", "Standard", "Horsebow"];
      $code = array_rand($archery_words);
      return $code = strtoupper($archery_words[$code] . rand(100, 1000) . $now->year);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }
}
