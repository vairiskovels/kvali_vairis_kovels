<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $colors = [
            ['color_code'    => '#ff6384'],
            ['color_code'    => '#36a2eb'],
            ['color_code'    => '#ffc53a'],
            ['color_code'    => '#4bc0c0'],
            ['color_code'    => '#ff9f40'],
            ['color_code'    => '#9966ff'],
            ['color_code'    => '#445af7'],
            ['color_code'    => '#afafaf']
        ];

        foreach ($colors as $color) {
            Color::create($color);
        }
    }
}
