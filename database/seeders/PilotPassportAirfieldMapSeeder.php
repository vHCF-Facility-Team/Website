<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PilotPassportAirfieldMapSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('pilot_passport_airfield_map')->insert([
             'airfield' => 'HNL',
             'mapped_to' => 1
         ]);
        DB::table('pilot_passport_airfield_map')->insert([
           'airfield' => 'JRF',
           'mapped_to' => 1
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'NGF',
            'mapped_to' => 1
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'LIH',
            'mapped_to' => 1
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'BKH',
            'mapped_to' => 1
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'MKK',
            'mapped_to' => 1
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'OGG',
            'mapped_to' => 1
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'KOA',
            'mapped_to' => 1
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'ITO',
            'mapped_to' => 1
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'GUM',
            'mapped_to' => 2
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'UAM',
            'mapped_to' => 2
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'ROP',
            'mapped_to' => 2
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'TIQ',
            'mapped_to' => 2
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'SPN',
            'mapped_to' => 2
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'BSF',
            'mapped_to' => 3
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'MUE',
            'mapped_to' => 3
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'UPP',
            'mapped_to' => 3
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'HNM',
            'mapped_to' => 3
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'JHM',
            'mapped_to' => 3
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'LNY',
            'mapped_to' => 3
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'LUP',
            'mapped_to' => 3
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'HHI',
            'mapped_to' => 3
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'HDH',
            'mapped_to' => 3
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'HPV',
            'mapped_to' => 3
        ]);
        DB::table('pilot_passport_airfield_map')->insert([
            'airfield' => 'PAK',
            'mapped_to' => 3
        ]);
    }
}
