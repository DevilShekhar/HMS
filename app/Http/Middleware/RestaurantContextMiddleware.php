<?php


namespace App\Http\Middleware;

use Closure;
use App\Models\Restaurant;

class RestaurantContextMiddleware
{
    public function handle($request, Closure $next)
    {
        $restaurant = $request->route('restaurant');

        if (is_string($restaurant)) {
            $restaurant = Restaurant::query()->where('slug', $restaurant)->firstOrFail();
        }

        app()->instance('restaurant', $restaurant);

        return $next($request);
    }
}
