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
                <h2>Create Recipe</h2>
                <p>
                    Define ingredients and quantities required to prepare menu items.
                </p>
            </div>
            <div class="premium-head-actions">

                <a href="{{ url()->previous() }}" class="btn premium-btn ghost-btn">

                    <i class="fas fa-arrow-left"></i>
                    Back To Recipes

                </a>

            </div>

        </div>

    </section>

    <section class="section premium-dashboard pt-0">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($branchSlug)
            <form
                action="{{ route('branch.recipe.store', [
                    'restaurant' => $restaurantSlug,
                    'branch' => $branchSlug,
                ]) }}"
                method="POST">
            @else
                <form action="{{ route('restaurant.recipe.store', [
                    'restaurant' => $restaurantSlug,
                ]) }}"
                    method="POST">
        @endif

        @csrf

        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">

        <div class="card premium-block">

            <div class="card-header premium-card-header">

                <div>

                    <h4>
                        Recipe Information
                    </h4>

                    <p class="header-subtext">
                        Configure ingredients required for preparing menu items.
                    </p>

                </div>

            </div>

            <div class="card-body">

                <div class="row">

                    {{-- Restaurant --}}
                    <div class="col-md-6 mb-4">

                        <label>
                            Restaurant
                        </label>

                        <input type="text" class="form-control premium-input" value="{{ $restaurant->name }}" readonly>

                    </div>

                    {{-- Branch --}}
                    <div class="col-md-6 mb-4">

                        <label>
                            Branch
                        </label>

                        <select name="branch_id" class="form-control premium-input">

                            <option value="">
                                Select Branch
                            </option>

                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">
                                    {{ $branch->name }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                    {{-- Menu Item --}}
                    <div class="col-md-12 mb-4">

                        <label>
                            Menu Item
                        </label>

                        <select name="menu_item_id" class="form-control premium-input">

                            <option value="">
                                Select Menu Item
                            </option>

                            @foreach ($menuItems as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                    {{-- Ingredients Section --}}
                    <div class="col-md-12">

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <h5 class="mb-0">
                                Recipe Ingredients
                            </h5>

                            <button type="button" id="addIngredient" class="btn btn-success btn-sm">

                                <i class="fas fa-plus"></i>
                                Add Ingredient

                            </button>

                        </div>

                        <div id="recipeRows">

                            {{-- First Ingredient Row --}}
                            <div class="recipe-row border rounded p-3 mb-3">

                                <div class="row">

                                    <div class="col-md-4">

                                        <label>
                                            Inventory Item
                                        </label>

                                        <select name="inventory_id[]" class="form-control premium-input">

                                            <option value="">
                                                Select Ingredient
                                            </option>

                                            @foreach ($inventoryItems as $inventory)
                                                <option value="{{ $inventory->id }}">
                                                    {{ $inventory->name }}
                                                    ({{ $inventory->unit }})
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                    <div class="col-md-2">

                                        <label>
                                            Quantity
                                        </label>

                                        <input type="number" step="0.001" name="quantity_required[]"
                                            class="form-control premium-input" placeholder="30">

                                    </div>

                                    <div class="col-md-2">

                                        <label>
                                            Unit
                                        </label>

                                        <select name="recipe_unit[]" class="form-control premium-input">

                                            <option value="Gram">Gram</option>
                                            <option value="Kg">Kg</option>
                                            <option value="ML">ML</option>
                                            <option value="Litre">Litre</option>
                                            <option value="Piece">Piece</option>

                                        </select>

                                    </div>

                                    <div class="col-md-3">

                                        <label>
                                            Remarks
                                        </label>

                                        <input type="text" name="remarks[]" class="form-control premium-input"
                                            placeholder="Optional">

                                    </div>

                                    <div class="col-md-1 d-flex align-items-end">

                                        <button type="button" class="btn btn-danger removeRow">

                                            <i class="fas fa-trash"></i>

                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="mt-4">

            <button type="submit" class="btn btn-primary">

                Save Recipe

            </button>

        </div>

        </form>

    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.getElementById('addIngredient')
                .addEventListener('click', function() {

                    let firstRow =
                        document.querySelector('.recipe-row');

                    let clone =
                        firstRow.cloneNode(true);

                    clone.querySelectorAll('input')
                        .forEach(input => {
                            input.value = '';
                        });

                    clone.querySelectorAll('select')
                        .forEach(select => {
                            select.selectedIndex = 0;
                        });

                    document
                        .getElementById('recipeRows')
                        .appendChild(clone);

                });

            document.addEventListener('click', function(e) {

                if (e.target.closest('.removeRow')) {

                    let rows =
                        document.querySelectorAll('.recipe-row');

                    if (rows.length > 1) {

                        e.target
                            .closest('.recipe-row')
                            .remove();

                    }

                }

            });

        });
    </script>
@endsection
