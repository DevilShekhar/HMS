@extends('layouts.app')

@section('content')
    <section class="section premium-dashboard">
        <div class="premium-floating-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <span class="header-badge"
                            style="background: linear-gradient(135deg, #ff8a00, #ff5f00); color: white;">
                            Recipe Management
                        </span>
                        <h1>Edit Recipe</h1>
                        <p>Update ingredients and quantities required to prepare menu item.</p>
                    </div>
                </div>

                @php
                    $restaurantSlug = request()->route('restaurant');
                    $branchSlug = request()->route('branch');
                @endphp

                <div class="header-right">
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
                </div>
            </div>
        </div>
    </section>

    <section class="section premium-dashboard pt-0">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $restaurantSlug = request()->route('restaurant');
            $branchSlug = request()->route('branch');
            $firstRecipe = $recipes->first();
        @endphp

        @if ($branchSlug)
            <form action="{{ route('branch.recipe.update', [
                'restaurant' => $restaurantSlug,
                'branch' => $branchSlug,
                'recipe' => $firstRecipe->menu_item_id ?? $firstRecipe->id,
            ]) }}" method="POST">
        @else
            <form action="{{ route('restaurant.recipe.update', [
                'restaurant' => $restaurantSlug,
                'recipe' => $firstRecipe->menu_item_id ?? $firstRecipe->id,
            ]) }}" method="POST">
        @endif

            @csrf
            @method('PUT')

            <input type="hidden" name="restaurant_id" value="{{ $restaurant->id ?? '' }}">

            <div class="card premium-block">

                <div class="card-header premium-card-header"
                     style="background: linear-gradient(135deg, #ff8a00, #ff5f00); color: white;">
                    <div>
                        <h4>Recipe Information</h4>
                        <p class="header-subtext">
                            Update ingredients required for preparing menu items.
                        </p>
                    </div>
                </div>

                <div class="card-body">

                    <div class="row">

                        {{-- Restaurant --}}
                        <div class="col-md-6 mb-4">
                            <label>Restaurant</label>
                            <input type="text" class="form-control premium-input"
                                   value="{{ $restaurant->name ?? '-' }}" readonly>
                        </div>

                        {{-- Branch --}}
                        <div class="col-md-6 mb-4">
                            <label>Branch</label>
                            <select name="branch_id" class="form-control premium-input" required>
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ $firstRecipe->branch_id == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Menu Item --}}
                        <div class="col-md-12 mb-4">
                            <label>Menu Item</label>
                            <select name="menu_item_id" class="form-control premium-input" required>
                                <option value="">Select Menu Item</option>
                                @foreach ($menuItems as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $firstRecipe->menu_item_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ingredients Section --}}
                        <div class="col-md-12">
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Recipe Ingredients</h5>
                                <button type="button" class="btn btn-success" id="addIngredient">
                                    <i class="fas fa-plus-circle"></i> Add Ingredient
                                </button>
                            </div>

                            <div id="recipeRows">
                                @foreach ($recipes as $recipe)
                                    <div class="recipe-row border rounded p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Inventory Item</label>
                                                <select name="inventory_id[]" class="form-control premium-input" required>
                                                    <option value="">Select Ingredient</option>
                                                    @foreach ($inventoryItems as $inventory)
                                                        <option value="{{ $inventory->id }}"
                                                            {{ $recipe->inventory_id == $inventory->id ? 'selected' : '' }}>
                                                            {{ $inventory->name }} ({{ $inventory->unit }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <label>Quantity</label>
                                                <input type="number" step="0.001" name="quantity_required[]"
                                                       value="{{ $recipe->quantity_required }}"
                                                       class="form-control premium-input" required>
                                            </div>

                                            <div class="col-md-2">
                                                <label>Unit</label>
                                                <select name="recipe_unit[]" class="form-control premium-input" required>
                                                    <option value="Gram" {{ $recipe->recipe_unit == 'Gram' ? 'selected' : '' }}>Gram</option>
                                                    <option value="Kg" {{ $recipe->recipe_unit == 'Kg' ? 'selected' : '' }}>Kg</option>
                                                    <option value="ML" {{ $recipe->recipe_unit == 'ML' ? 'selected' : '' }}>ML</option>
                                                    <option value="Litre" {{ $recipe->recipe_unit == 'Litre' ? 'selected' : '' }}>Litre</option>
                                                    <option value="Piece" {{ $recipe->recipe_unit == 'Piece' ? 'selected' : '' }}>Piece</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label>Remarks</label>
                                                <input type="text" name="remarks[]"
                                                       value="{{ $recipe->remarks ?? '' }}"
                                                       class="form-control premium-input" placeholder="Optional">
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
                </div>

                <div class="premium-card-footer">
                    <button type="submit" class="premium-btn btn-primary">
                        <i class="fas fa-save"></i> Update Recipe
                    </button>
                </div>

            </div>
        </form>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Add new ingredient row
            document.getElementById('addIngredient').addEventListener('click', function () {
                let firstRow = document.querySelector('.recipe-row');
                if (!firstRow) return;

                let clone = firstRow.cloneNode(true);

                // Clear values in cloned row
                clone.querySelectorAll('input').forEach(input => input.value = '');
                clone.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

                document.getElementById('recipeRows').appendChild(clone);
            });

            // Remove row
            document.addEventListener('click', function (e) {
                if (e.target.closest('.removeRow')) {
                    let rows = document.querySelectorAll('.recipe-row');
                    if (rows.length > 1) {
                        e.target.closest('.recipe-row').remove();
                    }
                }
            });
        });
    </script>
@endsection
