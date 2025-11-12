<x-people::layouts.master>
    @php
        $cover = $person->getFirstMediaUrl('cover','thumb') ?: $person->getFirstMediaUrl('cover');

        // Adresse & géométrie
        $address = $person->address;
        $point   = optional($address)->location;

        $lat = $point?->latitude;
        $lng = $point?->longitude;
        $hasMap = is_numeric($lat) && is_numeric($lng);

        $roleColor = match($person->role) {
            'resident' => 'emerald','consultant'=>'blue','supplier'=>'amber','authority'=>'indigo','academic'=>'violet',
            default => 'slate'
        };
    @endphp

    <flux:container class="max-w-4xl mx-auto my-10 bg-white rounded-2xl shadow overflow-hidden">
        {{-- Cover --}}
        @if($cover)
            <div class="relative h-64 overflow-hidden">
                <img src="{{ $cover }}" alt="{{ $person->name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
            </div>
        @endif

        {{-- Header --}}
        <header class="px-8 py-6 border-b bg-gray-50">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading level="1" size="xl" class="text-3xl font-extrabold tracking-tight">
                        {{ $person->name }}
                    </flux:heading>

                    <div class="mt-2">
                        <flux:badge :color="$roleColor" variant="pill" size="sm">
                            {{ \Illuminate\Support\Str::title($person->role) }}
                        </flux:badge>
                    </div>

                    @if($person->role_description)
                        <p class="mt-3 text-slate-600">{{ $person->role_description }}</p>
                    @endif
                </div>

                @if($cover)
                    <img src="{{ $cover }}" alt="" class="w-24 h-24 rounded-full ring-2 ring-white object-cover hidden sm:block">
                @endif
            </div>

            {{-- Tags --}}
            @if($person->tags->isNotEmpty())
                <div class="mt-4 flex flex-wrap gap-1">
                    @foreach($person->tags as $t)
                        @php
                            $href = \Illuminate\Support\Facades\Route::has('helpcenter.tags.show')
                                ? route('helpcenter.tags.show', $t).'?type=person'
                                : null;
                        @endphp
                        @if($href)
                            <a href="{{ $href }}" class="inline-block focus:outline-none focus:ring rounded">
                                <flux:badge :color="$t->color" variant="pill" size="xs">{{ $t->name }}</flux:badge>
                            </a>
                        @else
                            <flux:badge :color="$t->color" variant="pill" size="xs">{{ $t->name }}</flux:badge>
                        @endif
                    @endforeach
                </div>
            @endif
        </header>

        {{-- Articles (AVANT Carte & Adresse) --}}
        @if(($person->articles ?? collect())->isNotEmpty())
            <section class="px-8 pt-6 pb-8 border-b">
                <div class="flex items-center justify-between mb-4 mt-4">
                    <flux:heading level="2" size="xl" class="font-semibold text-2xl">
                        {!! __('<span class="text-slate-500 italic">Articles by</span> :name', ['name' => $person->name]) !!}
                    </flux:heading>
                    @php
                        $allUrl = \Illuminate\Support\Facades\Route::has('blog.articles.index')
                            ? route('blog.articles.index', ['author_id' => $person->id])
                            : null;
                    @endphp
                    @if($allUrl)
                        <a href="{{ $allUrl }}" class="text-sm text-slate-600 hover:underline">
                            {{ __('View all') }}
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($person->articles as $a)
                        @php
                            $href   = \Illuminate\Support\Facades\Route::has('blog.articles.show')
                                    ? route('blog.articles.show', $a)
                                    : '#';
                            $aCover = $a->getFirstMediaUrl('cover','small') ?: $a->getFirstMediaUrl('cover');
                        @endphp

                        <a href="{{ $href }}" class="block rounded-xl border bg-white hover:shadow focus:outline-none focus:ring">
                            @if($aCover)
                                <div class="relative h-40 overflow-hidden rounded-t-xl">
                                    <img src="{{ $aCover }}" alt="{{ $a->title }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                </div>
                            @endif

                            <div class="p-4">
                                <h3 class="font-semibold line-clamp-2">{{ $a->title }}</h3>
                                @if($a->excerpt)
                                    <p class="mt-1 text-sm text-slate-600 line-clamp-2">{{ $a->excerpt }}</p>
                                @endif

                                <div class="mt-2 text-xs text-slate-500 flex items-center gap-2">
                                    <flux:icon name="calendar" class="w-4 h-4" />
                                    <span>{{ optional($a->published_at ?? $a->created_at)->translatedFormat('j F Y') }}</span>
                                </div>

                                @if($a->tags->isNotEmpty())
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        @foreach($a->tags->take(5) as $t)
                                            @php
                                                $tagHref = \Illuminate\Support\Facades\Route::has('helpcenter.tags.show')
                                                    ? route('helpcenter.tags.show', $t).'?type=article'
                                                    : null;
                                            @endphp
                                            @if($tagHref)
                                                <a href="{{ $tagHref }}" class="inline-block focus:outline-none focus:ring rounded">
                                                    <flux:badge :color="$t->color" variant="pill" size="xs">{{ $t->name }}</flux:badge>
                                                </a>
                                            @else
                                                <flux:badge :color="$t->color" variant="pill" size="xs">{{ $t->name }}</flux:badge>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Documents (avant carte & adresse) --}}
        @if(($person->documents ?? collect())->isNotEmpty())
            <section class="px-8 pt-6 pb-8 border-b">
                <div class="flex items-center justify-between mb-4">
                    <flux:heading level="2" size="lg" class="font-semibold">
                        {{ __('Documents by :name', ['name' => $person->name]) }}
                    </flux:heading>
                    @php
                        $allUrl = \Illuminate\Support\Facades\Route::has('documents.index')
                            ? route('documents.index', ['person_id' => $person->id])
                            : null;
                    @endphp
                    @if($allUrl)
                        <a href="{{ $allUrl }}" class="text-sm text-slate-600 hover:underline">
                            {{ __('View all') }}
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($person->documents as $doc)
                        @php
                            $href   = \Illuminate\Support\Facades\Route::has('documents.show')
                                        ? route('documents.show', $doc)
                                        : '#';
                            $media  = $doc->getFirstMedia('file');
                            $ext    = $media?->extension ?? 'file';
                            $icon   = match(strtolower($ext)) {
                                'pdf','doc','docx' => 'document',
                                'xls','xlsx' => 'table-cells',
                                'png','jpg','jpeg','webp' => 'image',
                                'txt','md' => 'document-text',
                                default => 'document',
                            };
                        @endphp

                            <div class="flex flex-col py-4 gap-3 hover:shadow focus:outline-none focus:ring border rounded-xl">
                                <a href="{{ $href }}" class="flex gap-2 bg-white px-4">
                                    <div class="shrink-0">
                                        <flux:icon name="{{ $icon }}" class="w-6 h-6 text-slate-500" />
                                    </div>
                                    <div class="min-w-0">
                                    <h3 class="font-semibold line-clamp-2">{{ $doc->title }}</h3>
                                    @if($doc->excerpt)
                                        <p class="mt-1 text-sm text-slate-600 line-clamp-2">{{ $doc->excerpt }}</p>
                                    @endif
                                    <div class="mt-2 text-xs text-slate-500 flex items-center gap-2">
                                        <flux:icon name="calendar" class="w-4 h-4" />
                                        <span>{{ optional($doc->published_at ?? $doc->created_at)->translatedFormat('j F Y') }}</span>
                                    </div>

                                    @if($doc->tags->isNotEmpty())
                                        <div class="mt-3 flex flex-wrap gap-1">
                                            @foreach($doc->tags->take(5) as $t)
                                                @php
                                                    $tagHref = \Illuminate\Support\Facades\Route::has('helpcenter.tags.show')
                                                        ? route('helpcenter.tags.show', $t).'?type=document'
                                                        : null;
                                                @endphp
                                                @if($tagHref)
                                                    <a href="{{ $tagHref }}" class="inline-block focus:outline-none focus:ring rounded">
                                                        <flux:badge :color="$t->color" variant="pill" size="xs">
                                                            {{ $t->name }}
                                                        </flux:badge>
                                                    </a>
                                                @else
                                                    <flux:badge :color="$t->color" variant="pill" size="xs">{{ $t->name }}</flux:badge>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                </a>
                                <div class="flex items-end w-full px-4 justify-end">
                                    <flux:link size="xs" icon="arrow-down-tray" href="{{ route('documents.download', $doc) }}">
                                        @if($media && \Illuminate\Support\Facades\Route::has('documents.download'))
                                            {{ __('Download') }}
                                        @endif
                                    </flux:link>
                                </div>
                            </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Body: Contact & Carte (APRÈS articles) --}}
        <div class="px-8 py-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Infos --}}
            <div class="lg:col-span-1 space-y-4">
                <flux:heading level="2" size="lg" class="font-semibold">{{ __('Contact & Address') }}</flux:heading>
                <div class="text-sm text-slate-700">
                    @if($address)
                        <p>{{ $address->line1 }}</p>
                        @if($address->line2)<p>{{ $address->line2 }}</p>@endif
                        <p>{{ $address->postal_code }} {{ $address->city }}</p>
                        <p>{{ $address->region }} ({{ $address->country_code }})</p>
                        @if($hasMap)
                            <p class="mt-2 text-slate-500">
                                {{ __('Coordinates:') }} {{ number_format((float)$lat,5) }}, {{ number_format((float)$lng,5) }}
                            </p>
                        @endif
                    @else
                        <p class="text-slate-500">{{ __('No address set.') }}</p>
                    @endif
                </div>
            </div>

            {{-- Carte --}}
            <div class="lg:col-span-2">
                @if($hasMap)
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                    <div id="person-map" class="w-full h-80 rounded-lg border"></div>
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const lat = {{ json_encode($lat) }};
                            const lng = {{ json_encode($lng) }};
                            const map = L.map('person-map', { scrollWheelZoom: false }).setView([lat, lng], 13);

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '&copy; OpenStreetMap'
                            }).addTo(map);

                            const marker = L.marker([lat, lng]).addTo(map);
                            marker.bindPopup(`<strong>{{ e($person->name) }}</strong><br>{{ e($address?->city ?? '') }}`).openPopup();
                        });
                    </script>
                @else
                    <div class="w-full h-80 rounded-lg border grid place-items-center text-slate-400">
                        <flux:icon name="map" class="w-8 h-8" />
                        <p class="mt-2 text-sm">{{ __('No map available for this person.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </flux:container>
</x-people::layouts.master>
