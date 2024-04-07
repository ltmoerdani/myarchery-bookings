<?php

namespace Database\Seeders;

use App\Models\InternationalStates;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InternationalStatesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $file = __DIR__ . '/data_csv/states.csv';
    $delimiter = ',';
    $header = [
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

    $data = [];
    if (($handle = fopen($file, 'r')) !== FALSE) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
        $data[] = array_combine($header, $row);
      }
      fclose($handle);
    }

    $collection = collect($data);
    foreach ($collection->chunk(50) as $chunk) {
      InternationalStates::insert($chunk->toArray());
    }
  }
}
