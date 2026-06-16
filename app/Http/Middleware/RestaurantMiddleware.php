<?php

namespace App\Http\Middleware;


use Closure;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class RestaurantMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'super_admin') {
            return $next($request);
        }

        $slug = $request->route('restaurant');

        if ($slug instanceof Restaurant) {
            $restaurant = $slug;
        } else {
            $restaurant = Restaurant::query()->where('slug', $slug)->first();
        }

        if (!$restaurant) {
            abort(404);
        }

        app()->instance('restaurant', $restaurant);

        return $next($request);
    }
}
