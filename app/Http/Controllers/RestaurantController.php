<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    public function index()
{
    $restaurants = Restaurant::latest()->paginate(10);

    return view('admin.restaurants.index', compact('restaurants'));
}

    public function create()
    {
        return view('admin.restaurants.create');
    }

    public function store(Request $request)
    {
        Restaurant::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),           
            'status' => $request->status ?? 1,
        ]);

        return redirect()
            ->route('restaurants.index')
            ->with('success', 'Restaurant created successfully');
    }

    public function edit($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        return view('admin.restaurants.edit', compact('restaurant'));
    }

    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        $restaurant->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),            
        ]);

        return redirect()
            ->route('restaurants.index')
            ->with('success', 'Restaurant updated successfully');
    }

    public function destroy($id)
    {
        Restaurant::findOrFail($id)->delete();

        return redirect()
            ->route('restaurants.index')
            ->with('success', 'Restaurant deleted successfully');
    }
}