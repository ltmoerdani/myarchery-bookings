<?php

namespace Database\Seeders;

use App\Models\InternationalCities;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InternationalCitiesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $file = __DIR__ . '/data_csv/cities.csv';
    $delimiter = ',';
    $header = [
      'id',
      'name',
      'state_id',
      'state_code',
      'state_name',
      'country_id',
      'country_code',
      'country_name',
      'latitude',
      'longitude',
      'wikiDataId',
    ];

    if (($handle = fopen($file, 'r')) !== FALSE) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
        $data = array_combine($header, $row);
        InternationalCities::insert($data);
      }
      fclose($handle);
    }
  }
}
