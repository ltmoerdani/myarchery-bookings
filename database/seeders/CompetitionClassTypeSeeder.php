<?php

namespace Database\Seeders;

use App\Models\CompetitionClassType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompetitionClassTypeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      [
        'name' => 'Age',
        'description' => 'For class type Age',
      ],
      [
        'name' => 'Open',
        'description' => 'For class type Open',
      ],
      [
        'name' => 'Master',
        'description' => 'For class type Master',
      ],
    ];
    foreach ($data as $valueData) {
      CompetitionClassType::create($valueData);
    }
  }
}
