@extends('layouts.app')

@section('content')
    @php
        $restaurantSlug = request()->route('restaurant');
        $branchSlug = request()->route('branch');
    @endphp

    <section class="section premium-dashboard">

        <div class="premium-page-head">

            <div class="premium-page-title">

                <span class="mini-badge">
                    Recipe Management
                </span>

                <h2>Recipe Details</h2>

                <p>
                    View recipe ingredients and consumption details.
                </p>

            </div>

            <div class="premium-head-actions">

                @if ($branchSlug)
                    <a href="{{ route('branch.recipe.index', [
                        'restaurant' => $restaurantSlug,
                        'branch' => $branchSlug,
                    ]) }}"
                        class="btn premium-btn ghost-btn">

                        <i class="fas fa-arrow-left"></i>
                        Back To Recipes

                    </a>
                @else
                    <a href="{{ route('restaurant.recipe.index', [
                        'restaurant' => $restaurantSlug,
                    ]) }}"
                        class="btn premium-btn ghost-btn">

                        <i class="fas fa-arrow-left"></i>
                        Back To Recipes

                    </a>
                @endif

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
