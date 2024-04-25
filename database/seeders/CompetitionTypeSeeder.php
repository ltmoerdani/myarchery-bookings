<?php

namespace Database\Seeders;

use App\Models\CompetitionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompetitionTypeSeeder extends Seeder
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
        'name' => 'Individu Male',
        'description' => 'For Individu Male',
      ],
      [
        'name' => 'Individu Female',
        'description' => 'For Individu Female',
      ],
      [
        'name' => 'Team Male',
        'description' => 'For Team Male',
      ],
      [
        'name' => 'Team Female',
        'description' => 'For Team Female',
      ],
      [
        'name' => 'Official',
        'description' => 'For Official',
      ],
      [
        'name' => 'Mix Team',
        'description' => 'For Mix Team',
      ],
    ];
    foreach ($data as $valueData) {
      CompetitionType::create($valueData);
    }
  }
}
