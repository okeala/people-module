<?php

namespace Modules\People\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Modules\People\Models\Person;

class PeopleMediaSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('tmp');

        $this->command?->info('Attachement des photos aux personnes (collection cover)…');

        $people = Person::query()->get();

        foreach ($people as $p) {
            try {
                // On remplace systématiquement (ou commente cette ligne pour conserver l’existant)
                $p->clearMediaCollection('cover');

                // Essais successifs d’URL (portrait) basés sur le slug/email
                $seed = $p->email ?: $p->id;
                $candidates = [
                    // pravatar (aléatoire/seeded par ?u=)
                    "https://i.pravatar.cc/640?u={$seed}",
                    // ui-avatars (généré par nom)
                    "https://ui-avatars.com/api/?name=" . urlencode($p->name) . "&size=640&background=0D8ABC&color=fff",
                    // picsum (générique, pas un vrai portrait)
                    "https://picsum.photos/seed/{$seed}/640/640",
                ];

                $attached = false;
                foreach ($candidates as $i => $url) {
                    try {
                        $tmp = "tmp/person-{$p->id}-{$i}.jpg";
                        $res = Http::timeout(10)->get($url);
                        if ($res->successful() && $res->body()) {
                            Storage::disk('public')->put($tmp, $res->body());
                            $p->addMedia(storage_path("app/public/{$tmp}"))
                                ->preservingOriginal()
                                ->toMediaCollection('cover');
                            $attached = true;
                            break;
                        }
                    } catch (\Throwable $e) {
                        // on essaye l’URL suivante
                    }
                }

                // Fallback local si aucun HTTP n’a fonctionné
                if (! $attached) {
                    $local = glob(base_path('modules/People/Database/Seeders/_assets/avatars/*.*')) ?: [];
                    if ($local) {
                        $file = Arr::random($local);
                        $p->addMedia($file)->preservingOriginal()->toMediaCollection('cover');
                        $attached = true;
                    }
                }

                $this->command?->line(($attached ? '✅' : '⚠️') . " {$p->name}");
            } catch (\Throwable $e) {
                $this->command?->warn("⚠️  Échec pour {$p->name}: {$e->getMessage()}");
            }
        }

        $this->command?->info('Photos attachées aux personnes.');
    }
}
