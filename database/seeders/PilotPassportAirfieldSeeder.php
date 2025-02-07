<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PilotPassportAirfieldSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('pilot_passport_airfield')->insert([
             'id' => 'HNL',
             'latitude' => 21.3178,
             'longitude' => -157.9202,
             'elevation' => 13,
             'name' => 'Daniel K. Inouye International Airport',
             'description' => "The Daniel K. Inouye International Airport (HNL) is the largest airport in the State of Hawaii and is located in Honolulu on the island of O’ahu. From Honolulu you can fly to neighbor island airports including Kahului Airport, Kapalua Airport and Hana Airport on Maui; Lihu’e Airport on Kaua’i; Kona International Airport at Keahole, Hilo International Airport and Waimea-Kohala Airport on the island of Hawai’i; Lana’i Airport on Lana’i; and Molokai Airport and Kalaupapa Airport on Molokai. Daniel K InouyeThe airport is proudly named after Hawaii’s late Senator Inouye."

         ]);
        DB::table('pilot_passport_airfield')->insert([
           'id' => 'JRF',
           'latitude' => 21.3122,
           'longitude' => -158.0728,
           'elevation' => 30,
           'name' => 'Kalaeloa (John Rodgers Field)',
           'description' => 'Kalaeloa Airport, also called John Rodgers Field and formerly Naval Air Station Barbers Point, is a joint civil-military regional airport of the State of Hawaiʻi established on July 1, 1999, to replace the Ford Island NALF facilities which closed on June 30 of the same year.'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'HHI',
            'latitude' => 21.4822,
            'longitude' => -158.0379,
            'elevation' => 834,
            'name' => 'Wheeler AAF',
            'description' => "Wheeler Army Airfield, also known as Wheeler Field and formerly as Wheeler Air Force Base, is a United States Army post located in the City & County of Honolulu and in the Wahiawa District of the Island of O'ahu, Hawaii."
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'HDH',
            'latitude' => 21.5794,
            'longitude' => -158.2023,
            'elevation' => 14,
            'name' => 'Dillingham Kawaihapai Air Field',
            'description' => 'Dillingham Airfield is a public and military use airport located two nautical miles west of the central business district of Mokulēʻia, in Honolulu County on the North Shore of Oʻahu in the U.S. state of Hawaii.'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'NGF',
            'latitude' => 21.4514,
            'longitude' => -157.7660,
            'elevation' => 23,
            'name' => 'Kaneohe Bay MCAS (Marion E Carl Field)',
            'description' => 'Marine Corps Air Station Kaneohe Bay or MCAS Kaneohe Bay is a United States Marine Corps airfield located within the Marine Corps Base Hawaii complex, formerly known as Marine Corps Air Facility Kaneohe Bay or Naval Air Station Kaneohe Bay'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'LIH',
            'latitude' => 21.9789,
            'longitude' => -159.3440,
            'elevation' => 152,
            'name' => 'Lihue',
            'description' => "This modest airport with gardens offers flights to mainland U.S. cities & between Hawaii's islands."
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'BKH',
            'latitude' => 22.0359,
            'longitude' => -159.7822,
            'elevation' => 23,
            'name' => 'Barking Sands Pmrf',
            'description' => 'The Pacific Missile Range Facility, Barking Sands (IATA: BKH, ICAO: PHBK, FAA LID: BKH) is a U.S. naval facility and airport located five nautical miles (9 km) northwest of the central business district of Kekaha, in Kauai County, Hawaii, United States'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'PAK',
            'latitude' => 21.8975,
            'longitude' => -159.5996,
            'elevation' => 24,
            'name' => 'Port Allen Airport',
            'description' => "Port Allen Airport is a regional airport of the State of Hawai'i. It is located 1 nautical mile southwest of the unincorporated town of Hanapepe on the south shore of the island of Kauaʻi"
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'MKK',
            'latitude' => 21.1557,
            'longitude' => -157.0933,
            'elevation' => 453,
            'name' => 'Molokai',
            'description' => 'Molokai Airport, also known as Hoolehua Airport is a state-owned, public use airport located six nautical miles northwest of Kaunakakai, on the island of Molokai in Maui County, Hawaii, United States. It is the principal airport of the island.'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'LUP',
            'latitude' => 21.2099,
            'longitude' => -156.9752,
            'elevation' => 23,
            'name' => 'Kalaupapa',
            'description' => "Kalaupapa Airport is a regional public use airport of the state of Hawaii, located on the northern peninsula of the island of Molokaʻi, two nautical miles north of Kalaupapa Settlement, in Kalawao County."
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'LNY',
            'latitude' => 20.7917,
            'longitude' => -156.9500,
            'elevation' => 1308,
            'name' => 'Lanai',
            'description' => 'Lanai Airport, also written as Lānaʻi Airport, is a state-owned public-use airport located three nautical miles or about 3.4 miles southwest of the central business district of Lanai City, in Maui County, Hawaii. The airport began regular operations in 1930. It is the only airport serving the island of Lanai.'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'OGG',
            'latitude' => 20.8949,
            'longitude' => -156.4363,
            'elevation' => 55,
            'name' => 'Kahului',
            'description' => 'Kahului Airport is the main airport of Maui in the State of Hawaii, United States, located east of Kahului. It has offered full airport operations since 1952'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'HNM',
            'latitude' => 20.7946,
            'longitude' => -156.0144,
            'elevation' => 77,
            'name' => 'Hana',
            'description' => 'Hana Airport is a regional public use airport of the State of Hawaiʻi on the east shore of the island of Maui, three nautical miles northwest of the unincorporated town of Hana. The airport was officially opened on November 11, 1950'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'UPP',
            'latitude' => 20.2652,
            'longitude' => -155.8577,
            'elevation' => 96,
            'name' => 'Upolo',
            'description' => 'Upolu Airport is a regional airport in Hawaii County, Hawaii, US. Located on the northern tip of the Big Island, it is 3 nautical miles northwest of the unincorporated town of Hawi.'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'MUE',
            'latitude' => 19.9986,
            'longitude' => -155.6737,
            'elevation' => 2671,
            'name' => 'Waimea-Kohala',
            'description' => 'Waimea-Kohala Airport is a state-owned public-use airport located one nautical mile southwest of Waimea, an unincorporated town in Hawaii County, Hawaii, United States. Hawaiian Airlines began scheduled passenger service from the airport in November 1953.'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'KOA',
            'latitude' => 19.7386,
            'longitude' => -156.0425,
            'elevation' => 48,
            'name' => 'Ellison Onizuka',
            'description' => 'Ellison Onizuka Kona International Airport at Keāhole is the primary airport on the Island of Hawaiʻi, located in Kailua-Kona, Hawaii, United States. The airport serves leeward Hawaiʻi island, including the resorts in North Kona and South Kohala.'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'BSF',
            'latitude' => 19.7613,
            'longitude' => -155.5543,
            'elevation' => 6190,
            'name' => 'Bradshaw Army Airfield',
            'description' => 'A small military airstrip known as Bradshaw Army Airfield. The airstrip was constructed at the area from 1955 to 1956 and dedicated Aug of 1957, by the then Commanding General of the 25th Inf. Div. The runway is only 3,700 feet (1,100 m) long, which only accommodates small aircraft. Fog often restricts helicopters, which can also fly in from the larger bases on Oʻahu.'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'ITO',
            'latitude' => 19.7206,
            'longitude' => -155.0442,
            'elevation' => 37,
            'name' => 'Hilo',
            'description' => 'Hilo International Airport, formerly General Lyman Field, is an international airport located in Hilo, Hawaiʻi, United States. Owned and operated by the Hawaii Department of Transportation, the airport serves windward Hawaiʻi island including the districts of Hilo, Hāmākua and Kaʻū, and Puna.'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'GUM',
            'latitude' => 13.4857,
            'longitude' => 144.8002,
            'elevation' => 305,
            'name' => 'Antonio B. Won Pat',
            'description' => 'Antonio B. Won Pat International Airport, also known as Guam International Airport, is an airport located in Tamuning and Barrigada, three miles east of the capital city of Hagåtña in the United States territory of Guam'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'UAM',
            'latitude' => 13.5836,
            'longitude' => 144.9284,
            'elevation' => 617,
            'name' => 'Andersen AFB',
            'description' => 'Andersen Air Force Base is a United States Air Force base located primarily within the village of Yigo in the United States territory of Guam. The host unit at Andersen AFB is the 36th Wing, assigned to the Pacific Air Forces Eleventh Air Force.'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'GRO',
            'latitude' => 14.1763,
            'longitude' => 145.2442,
            'elevation' => 606,
            'name' => 'Benjamin Taisacan Manglona',
            'description' => 'Rota International Airport, also known as Benjamin Taisacan Manglona International Airport, is a public airport located on Rota Island in the United States Commonwealth of the Northern Mariana Islands, near the village of Sinapalo. The airport is owned by the Commonwealth Ports Authority.'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'TNI',
            'latitude' => 14.9990,
            'longitude' => 145.6247,
            'elevation' => 270,
            'name' => 'Tinian',
            'description' => 'Tinian International Airport, also known as West Tinian Airport, is a public airport located on Tinian Island in the United States Commonwealth of the Northern Mariana Islands. This airport is owned by Commonwealth Ports Authority.'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'GSN',
            'latitude' => 15.1197,
            'longitude' => 145.7281,
            'elevation' => 214,
            'name' => 'Francisco C. Ada',
            'description' => 'Saipan International Airport, also known as Francisco C. Ada/Saipan International Airport, is a public airport located on Saipan Island in the United States Commonwealth of the Northern Mariana Islands. The airport is owned by Commonwealth Ports Authority. Its airfield was previously known as Aslito and Isely Field'
        ]);
        DB::table('pilot_passport_airfield')->insert([
            'id' => 'JHM',
            'latitude' => 20.9623,
            'longitude' => -156.6750,
            'elevation' => 256,
            'name' => 'Kapalua',
            'description' => 'Kapalua Airport (IATA: JHM, ICAO: PHJH, FAA LID: JHM), also known as Kapalua–West Maui Airport, is a regional airport in the district of Mahinahina on the west side of Maui island in the state of Hawaii. It is located five nautical miles (9.3 km; 5.8 mi) north of Lahaina, in Maui County. Most flights to Kapalua Airport originate from commuter airports on the other Hawaiian islands by commercial commuter services, unscheduled air taxis, and general aviation.'
        ]);
    }
}
