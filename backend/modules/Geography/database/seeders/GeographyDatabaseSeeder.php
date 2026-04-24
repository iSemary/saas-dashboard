<?php

namespace Modules\Geography\Database\Seeders;

use Illuminate\Database\Seeder;

class GeographyDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            ProvinceSeeder::class,
            CitySeeder::class,
            TownSeeder::class,
            StreetSeeder::class,
        ]);
    }
}
