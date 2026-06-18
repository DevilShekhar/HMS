@can('view-restaurant')
    @extends('layouts.app')
    @section('content')
        <section class="section premium-dashboard">
            <div class="premium-page-head">
                <div class="premium-page-title">
                    <span class="mini-badge">
                        Restaurant Management
                    </span>
                    <h2>Restaurant List</h2>
                    <p> Manage all restaurants </p>
                </div>
                <div class="premium-head-actions">
                    <a href="{{ route('restaurants.create') }}" class="btn premium-btn btn-main-premium">
                        <i class="fas fa-plus"></i>
                        Add Restaurant
                    </a>
                </div>
            </div>
        </section>
        <section class="section premium-dashboard pt-0">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card premium-block">
                            <div class="card-header premium-card-header">
                                <div>
                                    <h4 class="mb-1"> All Restaurants</h4>
                                    <p class="header-subtext mb-0">Restaurant records</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Slug</th>
                                                <th>Status</th>
                                                <th width="220">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($restaurants as $restaurant)
                                                <tr>
                                                    <td>
                                                        {{ $restaurants->firstItem() + $loop->index }}
                                                    </td>
                                                    <td>
                                                        <strong>{{ $restaurant->name }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info"> {{ $restaurant->slug }} </span>
                                                    </td>
                                                    <td>
                                                        @if ($restaurant->status)
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    @if($restaurant->status == 1)
                                                    <td>
                                                        <div class="d-flex">
                                                            <a href="{{ route('restaurants.edit', $restaurant->id) }}"
                                                                class="btn btn-sm btn-primary mr-2">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('restaurants.destroy', $restaurant->id) }}"
                                                                method="POST" class="delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center"> No Restaurants Found </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    {{ $restaurants->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @push('scripts')
            @if (session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: '{{ session('success') }}',
                        timer: 2000,
                        showConfirmButton: false
                    });
                </script>
            @endif
            @if (session('error'))
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '{{ session('error') }}'
                    });
                </script>
            @endif
            <script>
                document.querySelectorAll('.delete-form').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Delete Restaurant?',
                            text: 'This action cannot be undone.',
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
            </script>
        @endpush
    @endsection

   @else
    @php
        abort(403);
    @endphp
@endcan

