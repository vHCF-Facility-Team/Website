<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PilotPassportSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('pilot_passport')->insert([
            'id' => 1,
            'title' => 'Hawaii Big Airports',
            'description' => 'This path visits the bigger airports of Hawaii.'
         ]);
        DB::table('pilot_passport')->insert([
           'id' => 2,
           'title' => 'Guam',
           'description' => 'This path visits the islands around Guam.'
        ]);
        DB::table('pilot_passport')->insert([
            'id' => 3,
            'title' => 'Hawaii Small Airports',
            'description' => 'This path visits the smaller airports of Hawaii.'
        ]);
    }
}
