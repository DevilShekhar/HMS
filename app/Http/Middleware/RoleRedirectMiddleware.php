<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectByRole
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $restaurant = app('restaurant');

        // OWNER → always stay at restaurant root
        if ($user->role === 'owner') {
            if ($request->route()->getName() === 'entry') {
                return redirect()->route('restaurant.dashboard', [
                    'restaurant' => $restaurant->slug
                ]);
            }
        }

        // BRANCH MANAGER → force branch context
        if ($user->role === 'branch_manager') {

            if (!$user->branch) {
                abort(403, 'No branch assigned');
            }

            // if accessing owner dashboard → redirect to branch dashboard
            if ($request->routeIs('restaurant.dashboard')) {
                return redirect()->route('branch.dashboard', [
                    'restaurant' => $restaurant->slug,
                    'branch' => $user->branch->slug
                ]);
            }
        }

        return $next($request);
    }
}
