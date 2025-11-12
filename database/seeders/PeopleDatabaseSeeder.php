<?php

namespace Modules\People\Database\Seeders;

use Illuminate\Database\Seeder;

class PeopleDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PeopleSeeder::class,
            PeopleAddressSeeder::class,
            PeopleMediaSeeder::class,
        ]);
    }
}
