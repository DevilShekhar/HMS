@extends('layouts.app')

@section('content')
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
                        <h1>Recipes</h1>
                        <p>Manage menu item recipes and stock consumption.</p>
                    </div>
                </div>

                @php
                    $restaurantSlug = request()->route('restaurant');
                    $branchSlug = request()->route('branch');
                @endphp

                <div class="header-right">
                    @can('create-recipe')
                        @if ($branchSlug)
                                    <a href="{{ route('branch.recipe.create', [
                                'restaurant' => $restaurantSlug,
                                'branch' => $branchSlug,
                            ]) }}" class="premium-back-btn"
                                        style="background: linear-gradient(135deg, #ff8a00, #ff5f00); color: white; border: none;">
                                        <i class="fas fa-plus"></i>
                                        Create Recipe
                                    </a>
                        @else
                                    <a href="{{ route('restaurant.recipe.create', [
                                'restaurant' => $restaurantSlug,
                            ]) }}" class="premium-back-btn"
                                        style="background: linear-gradient(135deg, #ff8a00, #ff5f00); color: white; border: none;">
                                        <i class="fas fa-plus"></i>
                                        Create Recipe
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
                    <h4>Recipe List</h4>
                    <p class="header-subtext">
                        View and manage all recipes.
                    </p>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table premium-table table-hover">
                        <thead>
                            <tr>
                                <th width="80">SrNo.</th>
                                <th>Menu Item</th>
                                <th>Total Ingredients</th>
                                <th>Branch</th>
                                <th>Status</th>
                                <th width="180" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                                @forelse($recipes as $recipe)
                                @php
                                    $ingredientCount = \App\Models\Recipe::where('menu_item_id', $recipe->menu_item_id)->count();
                                @endphp

                                <tr>
                                    <td class="align-middle">{{ $loop->iteration }}</td>
                                    <td class="align-middle">{{ $recipe->menuItem->name ?? '-' }}</td>
                                    <td class="align-middle">
                                        <span class="badge"
                                            style="background: linear-gradient(135deg, #ff8a00, #ff5f00); color: white;">
                                            {{ $ingredientCount }} Ingredients
                                        </span>
                                    </td>
                                    <td class="align-middle">{{ $recipe->branch->name ?? '-' }}</td>
                                    <td>
                                            @if (($recipe->status ?? '') == 'active')
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center justify-content-center gap-1 flex-nowrap">
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
                                                ]) }}" class="btn btn-info btn-sm">
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
                                                ]) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            {{-- Delete --}}
                                            <form action="{{ $branchSlug
                                                ? route('branch.recipe.destroy', [
                                                    'restaurant' => $restaurantSlug,
                                                    'branch' => $branchSlug,
                                                    'recipe' => $recipe->menu_item_id,
                                                ])
                                                : route('restaurant.recipe.destroy', [
                                                    'restaurant' => $restaurantSlug,
                                                    'recipe' => $recipe->menu_item_id,
                                                ]) }}" method="POST" class="delete-form m-0 p-0 d-inline">
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
                                    <td colspan="5" class="text-center py-4">
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
        document.addEventListener('DOMContentLoaded', function () {
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
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Deactivate Recipe?',
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
