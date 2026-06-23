@extends('layouts.app')

@section('content')
    @php
        $restaurantSlug = request()->route('restaurant');
        $branchSlug = request()->route('branch');

        $firstRecipe = $recipes->first();
    @endphp
    <section class="section premium-dashboard">

        <div class="premium-page-head">

            <div class="premium-page-title">
                <span class="mini-badge">
                    Recipe Management
                </span>
                <h2>Edit Recipe</h2>
                <p>
                    Update recipe details.
                </p>
            </div>

            @php
                $restaurantSlug = request()->route('restaurant');
            @endphp

            <div class="premium-head-actions">

                <a href="{{ route('restaurant.recipe.index', [
                    'restaurant' => $restaurantSlug,
                ]) }}"
                    class="btn premium-btn ghost-btn">

                    <i class="fas fa-arrow-left"></i>
                    Back To Recipes

                </a>

            </div>

        </div>

    </section>

    <section class="section premium-dashboard pt-0">

        @if ($branchSlug)
            <form
                action="{{ route('branch.recipe.update', [
                    'restaurant' => $restaurantSlug,
                    'branch' => $branchSlug,
                    'recipe' => $firstRecipe->id,
                ]) }}"
                method="POST">
            @else
                <form
                    action="{{ route('restaurant.recipe.update', [
                        'restaurant' => $restaurantSlug,
                        'recipe' => $firstRecipe->id,
                    ]) }}"
                    method="POST">
        @endif

        @csrf
        @method('PUT')

        <div class="card premium-block">

            <div class="card-header premium-card-header">
                <div>
                    <h4>Recipe Information</h4>
                    <p class="header-subtext">
                        Update recipe details.
                    </p>
                </div>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-4">

                        <label>Branch</label>

                        <select name="branch_id" class="form-control premium-input">

                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ $firstRecipe->branch_id == $branch->id ? 'selected' : '' }}>

                                    {{ $branch->name }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label>Menu Item</label>

                        <select name="menu_item_id" class="form-control premium-input">

                            @foreach ($menuItems as $item)
                                <option value="{{ $item->id }}"
                                    {{ $firstRecipe->menu_item_id == $item->id ? 'selected' : '' }}>

                                    {{ $item->name }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div id="recipeRows">

                        @foreach ($recipes as $recipe)
                            <div class="recipe-row border rounded p-3 mb-3">

                                <div class="row">


                                    <div class="col-md-4">

                                        <label>
                                            Inventory Item
                                        </label>

                                        <select name="inventory_id[]" class="form-control premium-input">

                                            @foreach ($inventoryItems as $inventory)
                                                <option value="{{ $inventory->id }}"
                                                    {{ $recipe->inventory_id == $inventory->id ? 'selected' : '' }}>

                                                    {{ $inventory->name }} ({{ $inventory->unit }})

                                                </option>
                                            @endforeach

                                        </select>

                                    </div>


                                    <div class="col-md-2">

                                        <label>
                                            Quantity
                                        </label>

                                        <input type="number" step="0.001" name="quantity_required[]"
                                            value="{{ $recipe->quantity_required }}" class="form-control premium-input">

                                    </div>


                                    <div class="col-md-2">

                                        <label>
                                            Unit
                                        </label>

                                        <select name="recipe_unit[]" class="form-control premium-input">


                                            <option value="Gram" {{ $recipe->recipe_unit == 'Gram' ? 'selected' : '' }}>
                                                Gram
                                            </option>


                                            <option value="Kg" {{ $recipe->recipe_unit == 'Kg' ? 'selected' : '' }}>
                                                Kg
                                            </option>


                                            <option value="ML" {{ $recipe->recipe_unit == 'ML' ? 'selected' : '' }}>
                                                ML
                                            </option>


                                            <option value="Litre" {{ $recipe->recipe_unit == 'Litre' ? 'selected' : '' }}>
                                                Litre
                                            </option>


                                            <option value="Piece" {{ $recipe->recipe_unit == 'Piece' ? 'selected' : '' }}>
                                                Piece
                                            </option>


                                        </select>

                                    </div>


                                    <div class="col-md-3">

                                        <label>
                                            Remarks
                                        </label>

                                        <input type="text" name="remarks[]" value="{{ $recipe->remarks }}"
                                            class="form-control premium-input">

                                    </div>


                                    <div class="col-md-1 d-flex align-items-end">

                                        <button type="button" class="btn btn-danger removeRow">

                                            <i class="fas fa-trash"></i>

                                        </button>

                                    </div>


                                </div>

                            </div>
                        @endforeach

                    </div>

                </div>

            </div>

            <div class="mt-4">

                <button type="submit" class="btn btn-primary">

                    Update Recipe

                </button>

            </div>

            </form>

    </section>
@endsection
