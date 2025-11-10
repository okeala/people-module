<?php

namespace Modules\People\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\People\Models\Person;
use Modules\Helpcenter\Models\Tag;

class PeopleSeeder extends Seeder
{
    /** Couleurs par familles de contenus (slugs normalisés) */
    private array $colorMap = [
        // Participation / communauté
        'wwoofing' => 'purple', 'volunteering' => 'purple', 'training' => 'purple',

        // Agro / systèmes vivants
        'farming' => 'green', 'agroforestry' => 'green', 'permaculture' => 'green',
        'orcharding' => 'green', 'hedgerows' => 'green', 'silvopasture' => 'green',
        'soil-health' => 'green', 'forestry' => 'green', 'stone-fruit' => 'green', 'harvesting' => 'green',

        // Eau / hydraulique
        'water-retention' => 'sky', 'irrigation' => 'sky', 'fertigation' => 'sky',
        'hydrology' => 'sky', 'gravity-irrigation' => 'sky', 'ram-pump' => 'sky',

        // Bâtiment naturel / chantier
        'natural-building' => 'amber', 'timber-framing' => 'amber', 'dry-stone' => 'amber',
        'architecture' => 'amber', 'site-planning' => 'amber', 'building-materials' => 'amber', 'timber' => 'amber',

        // GNSS / terrain
        'land-surveying' => 'cyan', 'gnss' => 'cyan', 'rtk' => 'cyan', 'ntrip' => 'cyan', 'sw-maps' => 'cyan',

        // GIS / carto
        'gis' => 'indigo', 'qgis' => 'indigo', 'postgis' => 'indigo', 'gdal' => 'indigo',
        'leaflet' => 'indigo', 'geojson' => 'indigo', 'epsg-3763' => 'indigo',

        // Légal / institutions
        'ren' => 'rose', 'rjue' => 'rose', 'rjren' => 'rose',
        'land-law' => 'rose', 'regional-planning' => 'rose', 'municipal-permits' => 'rose', 'environmental-permits' => 'rose',
        'ccdr-centro' => 'rose', 'apa' => 'rose',

        // Localités
        'belmonte' => 'slate', 'covilha' => 'slate', 'guarda' => 'slate', 'aldeia-do-souto' => 'slate',

        // Dev logiciel / web
        'web-development' => 'fuchsia', 'laravel' => 'fuchsia', 'livewire' => 'fuchsia', 'filament' => 'fuchsia', 'apis' => 'fuchsia',

        // Outillage / maintenance
        'gardening-tools' => 'zinc', 'power-tools' => 'zinc', 'tools' => 'zinc',
        'computer-hardware' => 'zinc', 'computer-materials' => 'zinc', 'computer-parts' => 'zinc', 'computer-cleaning' => 'zinc',
        'repair' => 'zinc', 'maintenance' => 'zinc', 'auto-repair' => 'zinc', 'auto-bodywork' => 'zinc',

        // Low-tech
        'low-tech' => 'stone',

        // Academia / éducation
        'academia' => 'violet', 'research' => 'violet', 'education' => 'violet', 'school' => 'violet',
    ];

    public function run(): void
    {
        // -- Featured profiles (tags = contenus, pas métiers) --
        $residents = [
            ['name' => 'Neca A',  'role' => 'resident', 'tags' => ['farming','agroforestry','water-retention','low-tech']],
            ['name' => 'Maria P', 'role' => 'resident', 'tags' => ['orcharding','hedgerows','permaculture','irrigation']],
            ['name' => 'Steph M', 'role' => 'resident', 'tags' => ['web-development','laravel','livewire','filament']],
            ['name' => 'Ana S',   'role' => 'resident', 'tags' => ['wwoofing','volunteering','natural-building']],
        ];

        $consultants = [
            ['name' => 'Engo. Jose G',    'role' => 'architect',     'tags' => ['architecture','ren','site-planning']],
            ['name' => 'Engo. Carlos L',  'role' => 'engineer',       'tags' => ['architecture','rjue','municipal-permits']],
            ['name' => 'Enga. Telma A',   'role' => 'agronomist',     'tags' => ['agronomy','fertigation','soil-health']],
            ['name' => 'Sr Fernando G',   'role' => 'farmer',         'tags' => ['orcharding','stone-fruit','harvesting']],
            ['name' => 'Sr Augusto M',    'role' => 'land-surveyor',  'tags' => ['land-surveying','belmonte']],
            ['name' => 'Eng Bruno F',     'role' => 'land-surveyor',  'tags' => ['land-surveying','covilha']],
            ['name' => 'Sr Alexander F',  'role' => 'land-surveyor',  'tags' => ['land-surveying','guarda']],
            ['name' => 'Dra. Carina F',   'role' => 'legal-advisor',  'tags' => ['land-law','ren','environmental-permits']],
        ];

        $suppliers = [
            ['name' => 'Serraria de Leandres (Sawmill)', 'role' => 'supplier', 'tags' => ['timber-framing','natural-building','timber']],
            ['name' => 'Fernandes & Fernandes',          'role' => 'supplier', 'tags' => ['building-materials']],
            ['name' => 'Os Milagres',                    'role' => 'supplier', 'tags' => ['gardening-tools','power-tools']],
            ['name' => 'Os Pintos',                      'role' => 'supplier', 'tags' => ['building-materials','tools']],
            ['name' => 'Computer Parts',                 'role' => 'supplier', 'tags' => ['computer-hardware','repair','belmonte']],
            ['name' => 'Sr Moises S',                    'role' => 'supplier', 'tags' => ['auto-bodywork','auto-repair','belmonte']],
            ['name' => 'Douglas & Co',                   'role' => 'supplier', 'tags' => ['computer-parts','computer-cleaning','covilha']],
        ];

        $authorities = [
            ['name' => 'CCDR Centro',                        'role' => 'authority', 'tags' => ['ccdr-centro','rjren','regional-planning']],
            ['name' => 'APA',                                'role' => 'authority', 'tags' => ['apa','water-retention','environmental-permits']],
            ['name' => 'Camara da Covilha',                  'role' => 'authority', 'tags' => ['rjue','municipal-permits']],
            ['name' => 'University of Coimbra – Forestry',   'role' => 'academic',  'tags' => ['academia','forestry','silvopasture','research']],
            ['name' => 'University of Beira Interior',       'role' => 'academic',  'tags' => ['academia','research','covilha']],
            ['name' => 'Escola Agricola da Lajeosa',         'role' => 'academic',  'tags' => ['education','school','aldeia-do-souto']],
            ['name' => 'Junta da Freguesia Aldeia do Souto', 'role' => 'authority', 'tags' => ['local-government','aldeia-do-souto']],
            ['name' => 'IPMA – Hydrology',                   'role' => 'authority', 'tags' => ['hydrology','dem-contours','lidar']],
        ];

        $groups = [$residents, $consultants, $suppliers, $authorities];

        // Création via Factory + tags contenus (PAS d'injection de catégorie)
        foreach ($groups as $group) {
            foreach ($group as $p) {
                $person = $this->createPersonViaFactory($p['name'], $p['role']);
                $this->attachTags($person, array_values(array_unique(array_map(
                    fn ($t) => $this->normalizeContentSlug($t),
                    $p['tags'] ?? []
                ))));
            }
        }

        // Population aléatoire (contenus)
        $this->bulkRandom('resident',   8, ['farming','permaculture','orcharding','hedgerows','low-tech']);
        $this->bulkRandom('consultant', 6, ['architecture','site-planning','gis','qgis','land-surveying','rjue','ren']);
        $this->bulkRandom('supplier',   5, ['building-materials','tools','timber-framing','natural-building','computer-hardware']);
        $this->bulkRandom('authority',  4, ['academia','research','ccdr-centro','apa','regional-planning']);
    }

    /** Création via Factory (Person étend User) */
    protected function createPersonViaFactory(string $name, string $role): Person
    {
        $email = Str::slug($name).'@people.qapas.local';

        if ($p = Person::where('email', $email)->first()) {
            if ($p->role !== $role) $p->forceFill(['role' => $role])->save();
            return $p;
        }

        return Person::factory()->state([
            'name' => $name, 'email' => $email, 'role' => $role,
        ])->create();
    }

    /** Normalise un tag "contenu" vers un slug canonique */
    protected function normalizeContentSlug(string $raw): string
    {
        $s = Str::slug($raw);

        // mapping métiers -> contenus
        $map = [
            'farmer' => 'farming',
            'orchard' => 'orcharding',
            'wwoofer' => 'wwoofing',
            'volunteer' => 'volunteering',
            'land-surveyor' => 'land-surveying',
            'gis-technician' => 'gis',
            'web-developer' => 'web-development',
            'peach' => 'stone-fruit',
            'fruit' => 'orcharding',
            'engineer' => 'architecture', // ou 'engineering' si tu préfères
            'architect' => 'architecture',
            'legal-advisor' => 'land-law',
            'computer-materials' => 'computer-hardware',
            'computer-parts' => 'computer-hardware',
            'computer-cleaning' => 'computer-cleaning', // reste tel quel
            'body' => 'auto-bodywork',
            'cars' => 'auto-repair',
        ];

        return $map[$s] ?? $s;
    }

    /** Attache / crée les tags (TYPE_GLOBAL) avec couleur par familles */
    protected function attachTags(Person $person, array $slugs): void
    {
        $ids = [];
        foreach ($slugs as $slug) {
            $ids[] = $this->upsertTagAndReturnId($slug);
        }
        $person->tags()->syncWithoutDetaching($ids);
    }

    /** Génère N personnes + contenus aléatoires */
    protected function bulkRandom(string $categoryRole, int $count, array $tagPool): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $name   = Str::title($categoryRole).' '.str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $person = Person::factory()->role($categoryRole)->create(['name' => $name]);
            $tags   = array_map(fn($t) => $this->normalizeContentSlug($t), $this->pickSome($tagPool));
            $this->attachTags($person, $tags);
        }
    }

    /** 1–3 tags aléatoires distincts */
    protected function pickSome(array $pool): array
    {
        $pool = array_values(array_unique(array_map(fn($x) => Str::slug($x), $pool)));
        shuffle($pool);
        $n = random_int(1, min(3, count($pool)));
        return array_slice($pool, 0, $n);
    }

    /** Upsert d’un tag contenu (TYPE_GLOBAL) avec couleur thématique, retourne l’ID */
    protected function upsertTagAndReturnId(string $slug): int
    {
        $norm  = Str::slug($slug);
        $attrs = [
            'name'  => $this->prettyName($norm),
            'color' => $this->colorMap[$norm] ?? 'slate',
        ];

        $tag = Tag::withTrashed()
            ->where('slug', $norm)
            ->where('type', Tag::TYPE_GLOBAL)
            ->first();

        if ($tag) {
            if ($tag->trashed()) $tag->restore();
            $tag->fill($attrs)->save();
            return $tag->id;
        }

        return Tag::create([
            'slug' => $norm,
            'type' => Tag::TYPE_GLOBAL,
            ...$attrs,
        ])->id;
    }

    /** Mise en forme du nom affiché (acronymes conservés) */
    protected function prettyName(string $slug): string
    {
        $acronyms = [
            'ren' => 'REN', 'rjue' => 'RJUE', 'rjren' => 'RJREN', 'apa' => 'APA',
            'gnss' => 'GNSS', 'rtk' => 'RTK', 'ntrip' => 'NTRIP', 'gis' => 'GIS',
            'qgis' => 'QGIS', 'gdal' => 'GDAL', 'epsg-3763' => 'EPSG:3763',
        ];
        return $acronyms[$slug] ?? Str::title(str_replace('-', ' ', $slug));
    }
}
