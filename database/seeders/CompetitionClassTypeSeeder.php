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
        'name' => 'Umum',
        'description' => 'For class type Umum',
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
