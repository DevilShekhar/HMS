<?php

namespace App\Http\Middleware;


use Closure;
use App\Models\Restaurant;

class RestaurantMiddleware
{
    public function handle($request, Closure $next)
    {
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
