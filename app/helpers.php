<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('currentRestaurantSlug')) {
    function currentRestaurantSlug()
    {
        return request()->route('restaurant')
            ?? optional(Auth::user()->restaurant)->slug;
    }
}

if (!function_exists('currentBranchSlug')) {
    function currentBranchSlug()
    {
        return request()->route('branch')
            ?? optional(Auth::user()->branch)->slug;
    }
}
