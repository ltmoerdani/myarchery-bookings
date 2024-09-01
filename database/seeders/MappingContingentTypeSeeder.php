<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContingentType;

class MappingContingentTypeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $contingentEvents = ContingentType::get();

    if (!empty($contingentEvents)) {
      foreach ($contingentEvents as $value) {
        $contingentEvent = ContingentType::find($value['id']);

        if (strtolower($value['contingent_type']) == 'open') {
          $contingentEvent['select_type'] = null;
          $contingentEvent['country_id'] = null;
          $contingentEvent['country'] = null;
          $contingentEvent['province_id'] = null;
          $contingentEvent['province'] = null;
          $contingentEvent['state_id'] = null;
          $contingentEvent['state'] = null;
          $contingentEvent['city_id'] = null;
          $contingentEvent['city'] = null;
        }

        if (strtolower($value['contingent_type']) != 'open') {
          switch (strtolower($value['select_type'])) {
            case 'country':
              $contingentEvent['country_id'] = null;
              $contingentEvent['country'] = null;
              $contingentEvent['province_id'] = null;
              $contingentEvent['province'] = null;
              $contingentEvent['state_id'] = null;
              $contingentEvent['state'] = null;
              $contingentEvent['city_id'] = null;
              $contingentEvent['city'] = null;
              break;
            case 'province':
              $contingentEvent['province'] = null;
              $contingentEvent['province_id'] = null;
              break;
            case 'state':
              $contingentEvent['province'] = null;
              $contingentEvent['province_id'] = null;
              break;
            case 'city/district':
              $contingentEvent['city_id'] = null;
              $contingentEvent['city'] = null;
              break;
            default:
              $contingentEvent['country_id'] = null;
              $contingentEvent['country'] = null;
              $contingentEvent['province_id'] = null;
              $contingentEvent['province'] = null;
              $contingentEvent['state_id'] = null;
              $contingentEvent['state'] = null;
              $contingentEvent['city_id'] = null;
              $contingentEvent['city'] = null;
              break;
          }
        }

        $contingentEvent->save();
      }
    }
  }
}
