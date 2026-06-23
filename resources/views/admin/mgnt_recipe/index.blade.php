@extends('layouts.app')

@section('content')
    <section class="section premium-dashboard">

        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">
                    Recipe Management
                </span>
                <h2>Recipes</h2>
                <p>
                    Manage menu item recipes and stock consumption.
                </p>
            </div>

            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

            @if ($branchSlug)
                <a href="{{ route('branch.recipe.create', [
                    'restaurant' => $restaurantSlug,
                    'branch' => $branchSlug,
                ]) }}"
                    class="btn premium-btn">
                    <i class="fas fa-plus"></i>
                    Create Recipe
                </a>
            @else
                <a href="{{ route('restaurant.recipe.create', [
                    'restaurant' => $restaurantSlug,
                ]) }}"
                    class="btn premium-btn">
                    <i class="fas fa-plus"></i>
                    Create Recipe
                </a>
            @endif
        </div>

    </section>

    <section class="section premium-dashboard pt-0">

        <div class="card premium-block">

            <div class="card-header premium-card-header">
                <div>
                    <h4>Recipe List</h4>
                    <p class="header-subtext">
                        View and manage all recipes.
                    </p>
                </div>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table premium-table">

                        <thead>
                            <tr>
                                <th>SrNo.</th>
                                <th>Menu Item</th>
                                <th>Total Ingredients</th>
                                <th>Branch</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($recipes as $recipe)
                                @php
                                    $ingredientCount = \App\Models\Recipe::where(
                                        'menu_item_id',
                                        $recipe->menu_item_id,
                                    )->count();
                                @endphp

                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        {{ $recipe->menuItem->name ?? '-' }}
                                    </td>

                                    <td>
                                        <span class="badge bg-primary text-white">
                                            {{ $ingredientCount }} Ingredients
                                        </span>
                                    </td>

                                    <td>
                                        {{ $recipe->branch->name ?? '-' }}
                                    </td>

                                    <td>

                                        <div class="d-flex align-items-center flex-nowrap gap-1">

                                            @php
                                                $restaurantSlug = request()->route('restaurant');
                                                $branchSlug = request()->route('branch');
                                            @endphp

                                            {{-- View --}}
                                            <a href="{{ $branchSlug
                                                ? route('branch.recipe.show', [
                                                    'restaurant' => $restaurantSlug,
                                                    'branch' => $branchSlug,
                                                    'recipe' => $recipe->menu_item_id,
                                                ])
                                                : route('restaurant.recipe.show', [
                                                    'restaurant' => $restaurantSlug,
                                                    'recipe' => $recipe->menu_item_id,
                                                ]) }}"
                                                class="btn btn-info btn-sm">

                                                <i class="fas fa-eye"></i>

                                            </a>

                                            {{-- Edit --}}
                                            <a href="{{ $branchSlug
                                                ? route('branch.recipe.edit', [
                                                    'restaurant' => $restaurantSlug,
                                                    'branch' => $branchSlug,
                                                    'recipe' => $recipe->menu_item_id,
                                                ])
                                                : route('restaurant.recipe.edit', [
                                                    'restaurant' => $restaurantSlug,
                                                    'recipe' => $recipe->menu_item_id,
                                                ]) }}"
                                                class="btn btn-warning btn-sm">

                                                <i class="fas fa-edit"></i>

                                            </a>

                                            {{-- Delete --}}
                                            <form
                                                action="{{ $branchSlug
                                                    ? route('branch.recipe.destroy', [
                                                        'restaurant' => $restaurantSlug,
                                                        'branch' => $branchSlug,
                                                        'recipe' => $recipe->menu_item_id,
                                                    ])
                                                    : route('restaurant.recipe.destroy', [
                                                        'restaurant' => $restaurantSlug,
                                                        'recipe' => $recipe->menu_item_id,
                                                    ]) }}"
                                                method="POST" class="delete-form m-0 p-0">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center">
                                        No Recipes Found
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </section>
@endsection
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.delete-form').forEach(form => {

            form.addEventListener('submit', function(e) {

                e.preventDefault();

                Swal.fire({
                    title: 'Deactivate Category?',
                    text: 'This action can be reverted later.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {

                    if (result.isConfirmed) {
                        form.submit();
                    }

                });

            });

        });

    });
</script>
