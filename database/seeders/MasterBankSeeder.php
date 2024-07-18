<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterBank;

class MasterBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = __DIR__ . '/data_csv/master_bank.csv';
        $delimiter = ',';
        $header = [
            'id',
            'type',
            'bank_code',
            'bank_name',
            'payment_method',
            'endpoint',
            'is_active',
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
            MasterBank::insert($chunk->toArray());  
        }
    }
}
