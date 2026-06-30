<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRouteAccess
{
       public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('restaurant.logout')) {
            return $next($request);
        }

        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        $routeName = $request->route()->getName();
        $restaurantSlug = $request->route('restaurant');
        $branchSlug = $request->route('branch');

        $isBranchManagementRoute = str_contains($routeName, 'restaurant.branches');

        if (in_array($user->role, ['branch_manager', 'chef', 'waiter', 'waiter_head'])) {

            if (!$branchSlug) {
                if ($user->restaurant_id && $user->branch?->restaurant_id) {
                    if ($user->restaurant_id !== $user->branch->restaurant_id) {
                        abort(403, 'Invalid restaurant access.');
                    }
                }
                return $next($request);
            }

            if ($branchSlug && $user->branch && $user->branch->slug !== $branchSlug) {
                abort(403, 'Invalid branch access.');
            }

            return $next($request);
        }

        if ($user->role === 'owner') {

            if ($branchSlug && !$isBranchManagementRoute) {
                abort(403, 'Owner cannot access branch URLs.');
            }

            if ($user->restaurant && $user->restaurant->slug !== $restaurantSlug) {
                abort(403, 'Invalid restaurant access.');
            }
        }

        // Super Admin / Others - allow
        return $next($request);
    }
}
