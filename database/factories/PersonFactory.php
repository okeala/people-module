<?php

namespace Modules\People\Database\Factories;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Modules\People\Models\Person;

class PersonFactory extends UserFactory
{
    protected $model = Person::class;

    public function definition(): array
    {
        $name = $this->faker->name();

        return [
            'name'              => $name,
            'email'             => Str::slug($name).'.'.$this->faker->unique()->numberBetween(1000, 9999).'@people.qapas.local',
            'password'          => Hash::make('password'),
            'role'              => 'resident',
            'role_description'  => null,
            'is_admin'          => false,
            'is_editor'         => false,
            'is_author'         => false,
            'is_contributor'    => false,
            'is_subscriber'     => false,

            // optionnels mais pratiques
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ];
    }

    /** Generic role setter */
    public function role(string $role): static
    {
        return $this->state(fn () => ['role' => $role]);
    }

    // Convenience states
    public function resident(): static   { return $this->role('resident'); }
    public function consultant(): static { return $this->role('consultant'); }
    public function supplier(): static   { return $this->role('supplier'); }
    public function authority(): static  { return $this->role('authority'); }
    public function academic(): static   { return $this->role('academic'); }

    /**
     * @usage Person::factory()->withPhoto()->create([...]);
     */
    public function withPhoto(): static
    {
        return $this->afterCreating(function (Person $p) {
            Storage::disk('public')->makeDirectory('tmp');

            $seed = $p->email ?: (string) $p->id;
            $url  = "https://i.pravatar.cc/640?u={$seed}";
            $tmp  = "tmp/person-{$p->id}.jpg";

            try {
                $res = Http::timeout(10)->get($url);
                if ($res->successful() && $res->body()) {
                    Storage::disk('public')->put($tmp, $res->body());
                    $p->addMedia(storage_path("app/public/{$tmp}"))
                        ->preservingOriginal()
                        ->toMediaCollection('cover');
                    return;
                }
            } catch (\Throwable $e) {
                // on tente un fallback
            }

            // Fallback léger (ui-avatars) si pravatar indisponible
            try {
                $url2 = "https://ui-avatars.com/api/?name=".urlencode($p->name)."&size=640&background=0D8ABC&color=fff";
                $res2 = Http::timeout(10)->get($url2);
                if ($res2->successful() && $res2->body()) {
                    Storage::disk('public')->put($tmp, $res2->body());
                    $p->addMedia(storage_path("app/public/{$tmp}"))
                        ->preservingOriginal()
                        ->toMediaCollection('cover');
                }
            } catch (\Throwable $e) {
                // silencieux: un seeder media séparé pourra compléter
            }
        });
    }

    /**
     * @usage Person::factory()->withAddress()->create([...]);
     */
    /**
     * @usage Person::factory()->withAddress()->create([...]);
     */
    public function withAddress(): static
    {
        return $this->afterCreating(function (\Modules\People\Models\Person $p) {
            if ($p->address()->exists()) return;

            $factory = \Modules\Helpcenter\Models\Address::factory()
                ->for($p, 'person');

            if ($p->role === 'resident') {
                $factory->qapas();
            } else {
                $factory->nearCovilha();
            }

            $factory->create();
        });
    }


}
