<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Farm;
use App\Models\Lookup_child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FarmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // 1. Fetch data: Get ONLY the farms that belong to the logged-in user.
        // We use the Eloquent relationship we defined earlier.
        $farms = Auth::user()->farms()
            ->with(['city', 'soilType']) // 1. Grabs the related data (no more nulls!)
            ->withCount(['workers', 'crops'])
            ->latest()                   // 2. Sorts by newest first
            ->paginate(6);              // 3. Breaks the results into pages of 10

        // 2. Return the Inertia View: This bridges Laravel to Vue.
        // 'Farms/Index' looks for a file at resources/js/Pages/Farms/Index.vue
        return Inertia::render('Farms/Index', [
            'farms' => $farms
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 1. Fetch geographic data
        $countries = Country::all(['id', 'name']);
        $cities = City::all(['id', 'name', 'country_id']); // Need country_id for frontend filtering

        // 2. Fetch Soil Types using your exact lookup logic converted to Eloquent
        $soilTypes = Lookup_child::whereHas('Lookup_parent', function ($query) {
            $query->where('name', 'Soil Type');
        })->get(['id', 'name']);

        return Inertia::render('Farms/Create', [
            'countries' => $countries,
            'cities' => $cities,
            'soilTypes' => $soilTypes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // By defining only these 5 fields, $validated drops country_id automatically
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'city_id'      => 'required|exists:cities,id',
            'soil_type_cd' => 'nullable|exists:lookup_children,id',
            'size_hectares' => 'nullable|numeric',
            'notes' => 'nullable|string|max:500',
        ]);

        // This is safe because $validated only contains name, city_id, and soil_type_cd
        Auth::user()->farms()->create($validated);

        return redirect()->route('farms.index')
            ->with('success', 'New agricultural farm plot saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Security Boundary: Ensure the logged-in user owns this farm
        abort_if($farm->user_id !== Auth::id(), 403, 'Unauthorized access.');

        // Quick-win redirection straight to your edit form
        return redirect()->route('farms.edit', $farm->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Farm $farm)
    {
        // Security Boundary: Prevent viewing another user's farm edit page
        abort_if($farm->user_id !== Auth::id(), 403, 'Unauthorized action.');

        $countries = Country::all(['id', 'name']);
        $cities = City::all(['id', 'name', 'country_id']); // Sent to frontend for reactive filtering

        // Fetch parent-child lookup data exactly like the create page
        $soilTypes = DB::table('lookup_children')
            ->join('lookup_parents', 'lookup_children.lookup_parent_id', '=', 'lookup_parents.id')
            ->where('lookup_parents.name', 'Soil Type')
            ->select('lookup_children.id', 'lookup_children.name')
            ->get();

        // Trace the existing country id via the farm's saved city relationship
        $currentCountryId = $farm->city ? $farm->city->country_id : null;

        return Inertia::render('Farms/Edit', [
            'farm'             => $farm,
            'countries'        => $countries,
            'cities'           => $cities,
            'soilTypes'        => $soilTypes,
            'currentCountryId' => $currentCountryId
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Farm $farm)
    {
        // Security Boundary
        abort_if($farm->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'city_id'      => 'required|exists:cities,id',
            'soil_type_cd' => 'nullable|exists:lookup_children,id',
            'size_hectares' => 'nullable|numeric',
            'notes' => 'nullable|string|max:500',
        ]);

        // Updates the record; 'country_id' is dropped automatically since it's not in $validated
        $farm->update($validated);

        return redirect()->route('farms.index')
            ->with('success', 'Farm profiles updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farm $farm)
    {
        // Security Boundary
        abort_if($farm->user_id !== Auth::id(), 403);

        $farm->delete();

        return redirect()->route('farms.index')
            ->with('success', 'Farm ecosystem permanently deleted.');
    }
}
