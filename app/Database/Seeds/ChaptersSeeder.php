<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ChaptersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['country' => 'Egypt', 'chapter_name' => 'WIFT Egypt'],
            ['country' => 'Ghana', 'chapter_name' => 'WIFT Ghana'],
            ['country' => 'Kenya', 'chapter_name' => 'WIFT Kenya'],
            ['country' => 'Morocco', 'chapter_name' => 'WIFT Morocco'],
            ['country' => 'Nigeria', 'chapter_name' => 'WIFT Nigeria'],
            ['country' => 'Senegal', 'chapter_name' => 'WIFT Senegal'],
            ['country' => 'South Africa', 'chapter_name' => 'WIFT South Africa'],
            ['country' => 'Tanzania', 'chapter_name' => 'WIFT Tanzania'],
            ['country' => 'Uganda', 'chapter_name' => 'WIFT Uganda'],
            ['country' => 'Zimbabwe', 'chapter_name' => 'WIFT Zimbabwe'],
            ['country' => 'Pan-African', 'chapter_name' => 'WIFT Africa (HQ)'],
        ];

        $this->db->table('chapters')->insertBatch($data);
    }
}
