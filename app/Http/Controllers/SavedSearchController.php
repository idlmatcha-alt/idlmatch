<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveSearchRequest;
use App\Models\SavedSearch;

class SavedSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function store(SaveSearchRequest $request)
    {
        $profile = $request->user()->profile;
        if (!$profile) {
            return redirect()->route('profile.photos');
        }
        $filters = $request->input('filters');

        if (is_string($filters)) {
            $filters = json_decode($filters, true) ?: [];
        }

        SavedSearch::create([
            'profile_id' => $profile->id,
            'name' => $request->input('name'),
            'filters_json' => $filters,
        ]);

        return redirect()->back()->with('status', 'Search saved.');
    }

    public function destroy(SavedSearch $savedSearch)
    {
        $this->authorize('delete', $savedSearch);
        $savedSearch->delete();

        return redirect()->back()->with('status', 'Saved search removed.');
    }
}
