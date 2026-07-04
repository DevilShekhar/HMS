@can('view-restaurant')
    @extends('layouts.app')
    @section('content')
   
        <section class="section premium-dashboard">
            <div class="premium-page-head">
                <div class="premium-page-title">
                     <span class="page-badge">
                        <i class="fas fa-store"></i>
                        Restaurant Management
                    </span>
                    <h2>Restaurant List</h2>
                    <p> Manage all restaurants </p>
                </div>
                <div class="premium-head-actions">
                    <a href="{{ route('restaurants.create') }}" class="btn btn-create">
                        <i class="fas fa-plus"></i>
                        Add Restaurant
                    </a>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <div>
                            <small>Total Restaurants</small>
                            <h3>{{ $restaurants->total() }}</h3>
                        </div>
                        <i class="fas fa-store icon"></i>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="dashboard-card success">
                        <div>
                            <small>Active</small>
                            <h3>{{ $restaurants->where('status',1)->count() }}</h3>
                        </div>
                        <i class="fas fa-check-circle icon"></i>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="dashboard-card danger">
                        <div>
                            <small>Inactive</small>
                            <h3>{{ $restaurants->where('status',0)->count() }}</h3>
                        </div>
                        <i class="fas fa-times-circle icon"></i>
                    </div>
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
                                    <table class="table table-striped table-hover" id="tableExport">
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
                                                        <div class="restaurant-info">
                                                            <div class="restaurant-avatar">
                                                                {{ strtoupper(substr($restaurant->name,0,1)) }}
                                                            </div>
                                                            <div>
                                                                <h6>{{ $restaurant->name }}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="slug-badge">
                                                        {{ $restaurant->slug }}
                                                        </span>
                                                        </td>
                                                    <td>
                                                        @if($restaurant->status)
                                                            <span class="status active">
                                                                <i class="fas fa-circle"></i>Active
                                                            </span>
                                                        @else
                                                            <span class="status inactive">
                                                                <i class="fas fa-circle"></i>Inactive
                                                            </span>
                                                        @endif
                                                    </td>                                                    
                                                    <td class="text-center">
                                                        <div class="action-buttons">
                                                            <a href="{{ route('restaurants.edit',$restaurant->id) }}" class="btn-action edit">
                                                                <i class="fas fa-pen"></i>
                                                            </a>
                                                            <form action="{{ route('restaurants.destroy',$restaurant->id) }}" method="POST" class="delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn-action delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>                                                   
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center"> No Restaurants Found </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
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

