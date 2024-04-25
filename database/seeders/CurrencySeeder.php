<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
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
        'name' => 'IDR',
        'symbol' => 'Rp',
        'symbol_position' => 'left',
        'text' => 'Rupiah',
        'text_position' => 'right',
        'rate' => '16500',
        'description' => 'currency from Indonesia',
      ],
      [
        'name' => 'USD',
        'symbol' => '$',
        'symbol_position' => 'left',
        'text' => 'USD',
        'text_position' => 'right',
        'rate' => '1',
        'description' => 'currency from USA',
      ],
    ];
    foreach ($data as $valueData) {
      Currency::create($valueData);
    }
  }
}
