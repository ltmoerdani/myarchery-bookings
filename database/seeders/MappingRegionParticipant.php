<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Participant;
use App\Models\InternationalCountries;
use App\Models\InternationalStates;
use App\Models\InternationalCities;
use App\Models\IndonesianProvince;
use App\Models\IndonesianCities;

class MappingRegionParticipant extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $participants = Participant::get();
    if (!empty($participants)) {
      foreach ($participants as $value) {
        $participant = Participant::find($value['id']);

        if (!empty($value['county_id'])) {
          $country = InternationalCountries::find($value['county_id']);

          $participant['county_id'] = empty($country) ? null : $country->id;
          $participant['country'] = empty($country) ? null : $country->name;

          // $participant['city_id'] = null;
          // $participant['city'] = null;

          if (!empty($participant['county_id'])) {
            if ($participant['county_id']  == "102") {
              $city = IndonesianCities::find($participant['city_id']);
              if (!empty($city)) {
                $participant['city_id'] = $city->id;
                $participant['city'] = $city->name;
              } else {
                $participant['city_id'] = null;
                $participant['city'] = null;
              }
            } else {
              $city = InternationalCities::find($participant['city_id']);
              if (!empty($city)) {
                $participant['city_id'] = $city->id;
                $participant['city'] = $city->name;
              } else {
                $participant['city_id'] = null;
                $participant['city'] = null;
              }
            }
          }
        } else {
          $participant['county_id'] = null;
          $participant['country'] = null;
          $participant['city_id'] = null;
          $participant['city'] = null;
        }

        $participant->save();
      }
    }
  }
}
