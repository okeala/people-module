<?php

namespace Modules\People\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Helpcenter\Models\Address;
use Modules\People\Models\Person;

class PeopleAddressSeeder extends Seeder
{
    public function run(): void
    {
        Person::query()->each(function (Person $p) {
            // 1) On génère un modèle Address (non persisté) via factory
            $draft = $p->role === 'resident'
                ? \Modules\Helpcenter\Models\Address::factory()->qapas()->make()
                : \Modules\Helpcenter\Models\Address::factory()->nearCovilha()->make();

            // 2) On upsert en conservant l’objet Point (SRID 4326) tel quel
            Address::updateOrCreate(
                ['person_id' => $p->id],
                [
                    'line1'        => $draft->line1,
                    'line2'        => $draft->line2,
                    'city'         => $draft->city,
                    'region'       => $draft->region,
                    'postal_code'  => $draft->postal_code,
                    'country_code' => $draft->country_code,
                    'location'     => $draft->location, // 👈 objet Point 4326, PAS de toArray()
                ]
            );
        });
    }
}
