<?php

namespace Database\Seeders;

use App\Models\CompetitionCategories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompetitionCategoriesSeeder extends Seeder
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
        'name' => 'Compound',
        'description' => 'Compound Categories',
      ],
      [
        'name' => 'Barebow',
        'description' => 'Barebow Categories',
      ],
      [
        'name' => 'Recurve',
        'description' => 'Recurve Categories',
      ],
      [
        'name' => 'Recurve/Standard',
        'description' => 'Recurve/Standard Categories',
      ],
      [
        'name' => 'Standard/Nasional',
        'description' => 'Standard/Nasional Categories',
      ],
      [
        'name' => 'Standard',
        'description' => 'Standard Categories',
      ],
      [
        'name' => 'Nasional',
        'description' => 'Nasional Categories',
      ],
      [
        'name' => 'Traditional',
        'description' => 'Traditional Categories',
      ],
      [
        'name' => 'Horsebow',
        'description' => 'Horsebow Categories',
      ],
      [
        'name' => 'Recurve/Nasional',
        'description' => 'Recurve/Nasional Categories',
      ],
    ];
    foreach ($data as $valueData) {
      CompetitionCategories::create($valueData);
    }
  }
}
