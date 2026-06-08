<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DesignerDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roomPackCount = \App\Models\RoomPack::where('designer_id', auth()->id())->count();
        $weather = null;
        $city = $request->query('city', 'Mumbai'); // Default city

        try {
            $geoResponse = Http::withHeaders([
                    'User-Agent' => 'LaravelApp'
                ])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $city,
                    'format' => 'json',
                    'limit' => 1,
                ]);

            $geoData = $geoResponse->json();

            if ($geoResponse->successful() && isset($geoData[0])) {
                $latitude = $geoData[0]['lat'];
                $longitude = $geoData[0]['lon'];

                $weatherResponse = Http::get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'current_weather' => true,
                ]);

                if ($weatherResponse->successful()) {
                    $weather = $weatherResponse->json();
                }
            }
        } catch (\Exception $e) {
            $weather = null;
        }

        return view('admin.dashboard', compact('roomPackCount', 'weather', 'city'));
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
