<?php

namespace Modules\People\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\People\Models\Person;

class PeopleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Modules/People/Http/Controllers/PeopleController.php
    public function index(\Illuminate\Http\Request $request)
    {
        $roles = cache()->remember('people.roles', 3600, fn () =>
        Person::query()
            ->whereNotNull('role')
            ->distinct()
            ->orderBy('role')
            ->pluck('role')
            ->all()
        );

        $people = Person::query()
            ->when($request->filled('role'), fn($q) => $q->where('role', $request->string('role')))
            ->with(['tags:id,name,slug,color','media','address'])
            ->latest('id')
            ->paginate($request->integer('per_page', 12))
            ->appends($request->query());

        return view('people::index', compact('people','roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::allowIf(auth()->user()['is_admin'] && auth()->user()->can('create', User::class));
        return view('people::people.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show(Person $person)
    {
        $user = auth()->user();
        $with = [
            'tags:id,name,slug,color',
            'media',
            'address',
        ];

        if (class_exists(\Modules\Blog\Models\Article::class)) {
            $with['articles'] = fn ($q) => $q->published()
                ->latest('published_at')
                ->with(['media','tags:id,name,slug,color'])
                ->limit(6);
        }

        if (class_exists(\Modules\Documents\Models\Document::class)) {
            $with['documents'] = fn ($q) => $q->published()
                ->visibleFor($user)
                ->latest('published_at')
                ->with(['media','tags:id,name,slug,color'])
                ->limit(6);
        }

        $person->load($with);

        return view('people::people.show', compact('person'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('people::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
