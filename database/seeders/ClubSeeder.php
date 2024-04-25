<?php

namespace Database\Seeders;

use App\Models\Clubs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $file = __DIR__ . '/data_csv/archery_clubs.csv';
    $delimiter = ',';
    $header = [
      'name',
      'logo',
      'banner',
      'place_name',
      'address',
      'description',
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
      Clubs::insert($chunk->toArray());
    }
  }
}
