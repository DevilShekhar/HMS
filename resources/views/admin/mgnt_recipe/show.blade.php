@extends('layouts.app')

@section('content')
    @php
        $restaurantSlug = request()->route('restaurant');
        $branchSlug = request()->route('branch');
    @endphp
    <section class="section premium-dashboard">
        <div class="premium-floating-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div>
                        <span class="header-badge"
                            style="background: linear-gradient(135deg, #ff8a00, #ff5f00); color: white;">
                            Recipe Management
                        </span>
                        <p>View recipe ingredients and consumption details.</p>
                    </div>
                </div>

                @php
                    $restaurantSlug = request()->route('restaurant');
                    $branchSlug = request()->route('branch');
                @endphp

                <div class="header-right">
                    @can('create-recipe')
                        @if ($branchSlug)
                                    <a href="{{ route('branch.recipe.index', [
                                'restaurant' => $restaurantSlug,
                                'branch' => $branchSlug,
                            ]) }}" class="premium-back-btn"
                                        style="background: linear-gradient(135deg, #ff8a00, #ff5f00); color: white; border: none;">
                                        <i class="fas fa-arrow-left"></i>
                                        Back To Recipes
                                    </a>
                        @else
                                    <a href="{{ route('restaurant.recipe.index', [
                                'restaurant' => $restaurantSlug,
                            ]) }}" class="premium-back-btn"
                                        style="background: linear-gradient(135deg, #ff8a00, #ff5f00); color: white; border: none;">
                                        <i class="fas fa-arrow-left"></i>
                                        Back To Recipes
                                    </a>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </section>
    
    <section class="section premium-dashboard pt-0">
        <div class="card premium-block">
            <div class="card-header premium-card-header">
                <div>
                    <h4>Recipe Information</h4>
                    <p class="header-subtext">
                        Complete recipe configuration.
                    </p>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Restaurant --}}
                    <div class="col-md-6 mb-4">
                        <label class="fw-bold">
                            Restaurant
                        </label>
                        <div>
                            {{ $recipes->first()?->restaurant?->name ?? '-' }}
                        </div>
                    </div>

                    {{-- Branch --}}
                    <div class="col-md-6 mb-4">
                        <label class="fw-bold">
                            Branch
                        </label>
                        <div>
                            {{ $recipes->first()?->branch?->name ?? '-' }}
                        </div>
                    </div>

                    {{-- Menu Item --}}
                    <div class="col-md-6 mb-4">
                        <label class="fw-bold">
                            Menu Item
                        </label>
                        <div>
                            {{ $recipes->first()?->menuItem?->name ?? '-' }}
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6 mb-4">
                        <label class="fw-bold">
                            Status
                        </label>
                        <div>
                            @if ($recipes->first()?->status == 'active')
                                <span class="badge bg-success">
                                    Active
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <hr>

                {{-- Ingredients Table --}}
                <div class="mt-4">
                    <h5 class="mb-3">
                        Ingredients List
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="60">#</th>
                                    <th>Ingredient</th>
                                    <th>Quantity Required</th>
                                    <th>Unit</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recipes as $recipe)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            {{ $recipe->inventory?->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $recipe->quantity_required }}
                                        </td>
                                        <td>
                                            {{ $recipe->recipe_unit }}
                                        </td>
                                        <td>
                                            {{ $recipe->remarks ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            No ingredients found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
