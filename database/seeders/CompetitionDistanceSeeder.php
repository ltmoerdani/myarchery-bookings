<?php

namespace Database\Seeders;

use App\Models\CompetitionDistance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompetitionDistanceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    for ($i = 1; $i <= 150; $i++) {
      $data = [
        'name' => $i,
        'description' => $i . ' Meter',
      ];

      CompetitionDistance::create($data);
    }
  }
}
