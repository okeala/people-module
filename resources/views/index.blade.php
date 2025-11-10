<x-people::layouts.master>
    <flux:container class="max-w-6xl mx-auto py-8">
        <header class="px-12 pb-8 border-b border-gray-200 text-center">
            <flux:heading level="1" size="xl" class="font-semibold text-3xl text-gray-900 tracking-tight mb-2">
                {{ __('Who is Who?') }}
            </flux:heading>

            <p class="text-lg text-gray-500 italic max-w-2xl mx-auto leading-relaxed">
                {{ __('We, the agro-residents of QAPAS, collaborate with many people—suppliers, organizations, authorities, and consultants from all backgrounds. We’re happy to have them alongside us.') }}
            </p>
        </header>

        @php
            $currentRole = request('role');
            $perPage = request('per_page');
            $roles = [
                ''            => __('All roles'),
                'resident'    => __('Residents'),
                'consultant'  => __('Consultants'),
                'supplier'    => __('Suppliers'),
                'authority'   => __('Authorities'),
                'academic'    => __('Academics'),
            ];
        @endphp

        {{-- Toolbar filtres --}}
        <div class="mt-6 mb-4 flex items-center gap-3">
            <form method="GET" class="flex items-center gap-2">
                @if($perPage)
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                @endif

                <label for="role" class="text-sm text-slate-600">{{ __('Filter by role') }}</label>
                <select id="role" name="role" class="border rounded px-2 py-1 text-sm" onchange="this.form.submit()">
                    @foreach($roles as $value => $label)
                        <option value="{{ $value }}" @selected($currentRole === $value || ($value==='' && $currentRole===null))>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                @if($currentRole)
                    <a href="{{ route('people.index') }}"
                       class="text-sm text-slate-500 hover:underline">
                        {{ __('Reset') }}
                    </a>
                @endif
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($people as $person)
                <div class="group rounded-2xl overflow-hidden border bg-white hover:shadow-md focus-within:ring">
                    {{-- Photo / cover + badge rôle --}}
                    <a href="{{ route('people.show', $person) }}" class="block" aria-label="{{ __(":person's profile", ['person' => $person->name]) }}">
                        <div class="relative aspect-square bg-slate-100">
                            @php
                                $cover = $person->getFirstMediaUrl('cover','thumb') ?: $person->getFirstMediaUrl('cover');
                                $roleColor = match($person->role) {
                                    'resident'   => 'emerald',
                                    'consultant' => 'blue',
                                    'supplier'   => 'amber',
                                    'authority'  => 'indigo',
                                    'academic'   => 'violet',
                                    default      => 'slate',
                                };
                            @endphp

                            @if($cover)
                                <img
                                    src="{{ $cover }}"
                                    alt="{{ $person->name }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    loading="lazy"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <flux:icon name="user" class="w-12 h-12" />
                                </div>
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent pointer-events-none"></div>

                            <div class="absolute top-3 right-3">
                                <div class="rounded-full bg-white/90 dark:bg-zinc-900/70 backdrop-blur-sm shadow-sm px-1.5 py-1">
                                    <flux:badge :color="$roleColor" variant="pill" size="xs" class="align-middle">
                                        {{ \Illuminate\Support\Str::title($person->role) }}
                                    </flux:badge>
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Infos --}}
                    <div class="p-4">
                        <a href="{{ route('people.show', $person) }}" class="inline-flex items-center gap-2 w-full">
                            <h3 class="text-base font-semibold text-slate-900 line-clamp-1">
                                {{ $person->name }}
                            </h3>
                            <flux:icon name="arrow-up-right" class="ml-auto text-zinc-400" variant="micro" />
                        </a>

                        @if($person->role_description)
                            <p class="mt-1 text-sm text-slate-600 line-clamp-2">
                                {{ $person->role_description }}
                            </p>
                        @endif

                        {{-- Tags cliquables --}}
                        @if($person?->tags?->isNotEmpty())
                            @php $max = 7; $total = $person->tags->count(); @endphp
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach($person->tags->take($max) as $tag)
                                    @php
                                        $href = \Illuminate\Support\Facades\Route::has('helpcenter.tags.show')
                                            ? route('helpcenter.tags.show', $tag).'?type=person'
                                            : (\Illuminate\Support\Facades\Route::has('tags.show')
                                                ? route('tags.show', $tag).'?type=person'
                                                : null);
                                    @endphp
                                    @if($href)
                                        <a href="{{ $href }}" class="inline-block focus:outline-none focus:ring rounded">
                                            <flux:badge :color="$tag->color" variant="pill" size="xs">{{ $tag->name }}</flux:badge>
                                        </a>
                                    @else
                                        <flux:badge :color="$tag->color" variant="pill" size="xs">{{ $tag->name }}</flux:badge>
                                    @endif
                                @endforeach

                                @if($total > $max)
                                    <span class="text-xs text-slate-500 px-2 py-1">+{{ $total - $max }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-slate-500 py-16">
                    <flux:icon name="users" class="w-8 h-8 mx-auto mb-2" />
                    {{ __('No people found.') }}
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if(method_exists($people, 'links'))
            <div class="mt-8">
                {{ $people->links() }}
            </div>
        @endif
    </flux:container>
</x-people::layouts.master>
