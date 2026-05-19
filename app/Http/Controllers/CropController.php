<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Farm;
use App\Models\Lookup_child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CropController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Securely query downstream through the authenticated user's farms
        $query = Crop::with(['farm', 'variety'])
            ->whereHas('farm', function ($q) {
                $q->where('user_id', Auth::id());
            });

        // --- FILTER 1: Text immediate search by Crop Name (using LIKE) ---
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // --- FILTER 2: Dropdown Selectors ---
        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }

        if ($request->filled('variety_id')) {
            $query->where('variety_cd', $request->variety_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // --- FILTER 3: Date Range Filter (Planting Date) ---
        if ($request->filled('start_date')) {
            $query->whereDate('planting_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('planting_date', '<=', $request->end_date);
        }

        // Fetch filtered results with counts for related harvests/inputs
        // (Assuming you have harvestDetails / inputDetails relationships setup)
        $crops = $query->withCount(['harvests', 'inputs'])
            ->latest()
            ->paginate(6)
            ->withQueryString();

        // Data lists to populate frontend searchable droplists
        $farms = Auth::user()->farms()->select('id', 'name')->get();

        // Fetch variety list from lookup_children table containing the 'link' column
        $varieties = Lookup_child::whereHas('Lookup_parent', function ($query) {
            $query->where('name', 'Variety');
        })->get(['id', 'name']);


        return Inertia::render('Crops/Index', [
            'crops' => $crops, // This is now a Paginated Object
            'farms' => $farms,
            'varieties' => $varieties,
            'statuses' => ['growing', 'harvested', 'failed', 'fallow'],
            'types' => ['annual', 'perennial'],
            'filters' => (object) $request->only(['search', 'farm_id', 'variety_id', 'type', 'status', 'start_date', 'end_date'])
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Crops/Create', [
            'farms' => Auth::user()->farms()->select('id', 'name')->get(),
            'varieties' => Lookup_child::whereHas('Lookup_parent', function ($query) {
                $query->where('name', 'Variety');
            })->get(['id', 'name']),
            'statuses' => ['growing', 'harvested', 'failed', 'fallow'],
            'types' => ['annual', 'perennial']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Execute Inline Validation
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'farm_id' => [
                'required',
                'exists:farms,id',
                // Security Check: Enforce that the selected farm plot belongs strictly to the logged-in user
                Rule::exists('farms', 'id')->where(function ($query) {
                    $query->where('user_id', Auth::id());
                })
            ],
            'variety_cd' => ['required', 'exists:lookup_children,id'],
            'type' => ['required', Rule::in(['annual', 'perennial'])],
            'status' => ['required', Rule::in(['growing', 'harvested', 'failed', 'fallow'])],
            'planting_date' => ['required', 'date'],
            'expected_harvest_date' => ['nullable', 'date', 'after_or_equal:planting_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            // Custom inline validation feedback messages
            'farm_id.exists' => 'The selected farm assignment area is invalid or unauthorized.',
            'variety_cd.exists' => 'The selected botanical variety must map to a certified taxonomy record.',
            'expected_harvest_date.after_or_equal' => 'The expected harvest date cannot occur before the initial planting timeline.',
        ]);

        try {
            // 2. Persist safely using only the validated array
            Crop::create($validatedData);

            // 3. Redirect back to ledger index with Flash parameters
            return redirect()
                ->route('crops.index')
                ->with('success', 'Crop cultivation record registered successfully!');
        } catch (\Exception $e) {
            // Log anomalies securely for debugging purposes
            Log::error("Failed to register crop entry: " . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected server error occurred while writing to the ledger.']);
        }
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
        // Security Check: Ensure the crop record exists and belongs downstream to the logged-in user's farms
        $crop = Crop::whereHas('farm', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        // Fetch only the farms that belong to the logged-in user for the form selector
        $farms = Auth::user()->farms()->select('id', 'name')->get();

        // Fetch the variety list from lookup_children table
        $varieties = Lookup_child::whereHas('Lookup_parent', function ($query) {
            $query->where('name', 'Variety');
        })->get(['id', 'name']);

        return Inertia::render('Crops/Edit', [
            'crop' => $crop,
            'farms' => $farms,
            'varieties' => $varieties,
            'statuses' => ['growing', 'harvested', 'failed', 'fallow'],
            'types' => ['annual', 'perennial']
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 1. Authorization Check: Ensure the crop belongs to the user before processing payload
        $crop = Crop::whereHas('farm', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        // 2. Execute Inline Validation
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'farm_id' => [
                'required',
                'exists:farms,id',
                // Security Check: Prevent changing a crop to a farm ID owned by someone else
                Rule::exists('farms', 'id')->where(function ($query) {
                    $query->where('user_id', Auth::id());
                })
            ],
            'variety_cd' => ['required', 'exists:lookup_children,id'],
            'type' => ['required', Rule::in(['annual', 'perennial'])],
            'status' => ['required', Rule::in(['growing', 'harvested', 'failed', 'fallow'])],
            'planting_date' => ['required', 'date'],
            'expected_harvest_date' => ['nullable', 'date', 'after_or_equal:planting_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            // Custom inline error responses
            'farm_id.exists' => 'The assigned farm field location is invalid or unauthorized.',
            'variety_cd.exists' => 'The specified crop variety taxonomy option does not exist.',
            'expected_harvest_date.after_or_equal' => 'The expected harvest timeline must occur on or after the initial planting date.',
        ]);

        try {
            // 3. Update the crop with validated modifications
            $crop->update($validatedData);

            // 4. Redirect back to ledger index with flash success parameters
            return redirect()
                ->route('crops.index')
                ->with('success', sprintf('The cultivation logs for "%s" have been modified successfully!', $crop->name));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to alter crop entry adjustments: " . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected server error halted updating the ledger record.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Security Check: Guard the record by isolating queries strictly downstream from the user's farms
        $crop = Crop::whereHas('farm', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        try {
            // Save the name prior to deletion for the browser confirmation feedback string
            $deletedCropName = $crop->name;

            $crop->delete();

            return redirect()
                ->route('crops.index')
                ->with('success', sprintf('The cultivation record for "%s" was successfully removed from the ledger.', $deletedCropName));
        } catch (\Exception $e) {
            Log::error("Failed to delete crop record entry: " . $e->getMessage());

            return back()->withErrors(['error' => 'An unexpected server error occurred while removing the ledger entry.']);
        }
    }
}
