<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            ->latest()                   // 2. Sorts by newest first
            ->paginate(5);              // 3. Breaks the results into pages of 10

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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
