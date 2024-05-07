<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DelegationType;

class DelegationTypeSeeder extends Seeder
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
                'name' => 'Club',
                'description' => 'Club Panahan',
            ],
            [
                'name' => 'School/Universities',
                'description' => 'Sekolah atau Universitas',
            ],
            [
                'name' => 'Country',
                'description' => 'Khusus Negara Tertentu',
            ],
            [
                'name' => 'Province',
                'description' => 'Khusus Negara dan Provinsi Tertentu',
            ],
            [
                'name' => 'City/District',
                'description' => 'Khusus Negara, Provinsi dan Kota Tertentu',
            ],
        ];

        foreach ($data as $valueData) {
            DelegationType::create($valueData);
        }
    }
}
