<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;

class UtilitiesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            // Core entities first (no dependencies)
            CategorySeeder::class,
            CurrencySeeder::class,
            IndustrySeeder::class,
            TagSeeder::class,
            TypeSeeder::class,
            AnnouncementSeeder::class,
            ReleaseSeeder::class,
            StaticPageSeeder::class,
            EntitySeeder::class,

            // Entities with dependencies
            UnitSeeder::class, // Depends on TypeSeeder
            StaticPageAttributeSeeder::class, // Depends on StaticPageSeeder
            ModuleEntitySeeder::class, // Depends on ModuleSeeder and EntitySeeder
        ]);
    }
}
