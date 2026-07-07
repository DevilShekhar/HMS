<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $countries = Country::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $countries->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('iso_code', 'like', "%{$search}%")
                    ->orWhere('currency_code', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $countries->where('status', $request->status);
        }

        $countries = $countries
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.countries.index', compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.countries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => 'required|string|max:5|unique:countries,iso_code',
            'currency_code' => 'required|string',
            'currency_symbol' => 'required|string|max:10',
            'timezone' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ]);
        $validated['status'] = (bool) $validated['status'];

        Country::create($validated);

        return redirect()
            ->route('countries.index')
            ->with('success', 'Country created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => 'required|string|max:5|unique:countries,iso_code,'.$country->id,
            'currency_code' => 'required|string|max:50',
            'currency_symbol' => 'required|string|max:10',
            'timezone' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $country->update($validated);

        return redirect()
            ->route('countries.index')
            ->with('success', 'Country updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->update([
            'status' => 0,
        ]);

        return redirect()
            ->route('countries.index')
            ->with('success', 'Country deactivated successfully.');
    }
}
