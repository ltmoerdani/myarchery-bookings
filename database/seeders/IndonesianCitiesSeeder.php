<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IndonesianCities;

class IndonesianCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = __DIR__ . '/data_csv/indonesian_cities.csv';
        $delimiter = ',';
        $header = [
            'id',
            'id_area',
            'kode',
            'province_id',
            'name',
            'level',
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
            IndonesianCities::insert($chunk->toArray());  
        }
    }
}
