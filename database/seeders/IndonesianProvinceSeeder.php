<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IndonesianProvince;

class IndonesianProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = __DIR__ . '/data_csv/indonesian_province.csv';
        $delimiter = ',';
        $header = [
            'id',
            'id_area',
            'kode',
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
            IndonesianProvince::insert($chunk->toArray());  
        }

    }
}
