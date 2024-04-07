<?php

namespace Database\Seeders;

use App\Models\InternationalCountries;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InternationalCountriesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $file = __DIR__ . '/data_csv/countries.csv';
    $delimiter = ',';
    $header = [
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

    $data = [];
    if (($handle = fopen($file, 'r')) !== FALSE) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
        $data[] = array_combine($header, $row);
      }
      fclose($handle);
    }

    $collection = collect($data);
    foreach ($collection->chunk(50) as $chunk) {
      InternationalCountries::insert($chunk->toArray());
    }
  }
}
