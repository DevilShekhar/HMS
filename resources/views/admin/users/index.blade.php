@can('view-user')
    @extends('layouts.app')
    @section('content')
        <section class="section premium-dashboard">
            <div class="premium-page-head">
                <div class="premium-page-title">
                    <span class="mini-badge">User Management</span>
                    <h2>User List</h2>
                    <p>Manage all users from API server</p>
                </div>
                @php
                    $restaurantSlug = request()->route('restaurant');
                    $branchSlug = request()->route('branch');
                @endphp

                @can('create-user')
                    @if (auth()->user()->role === 'super_admin')
                        <a href="{{ route('users.create') }}" class="btn premium-btn btn-main-premium">
                            <i class="fas fa-plus"></i>
                            Add User
                        </a>
                    @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                        <a href="{{ route('branch.users.create', [
                            'restaurant' => $restaurantSlug,
                            'branch' => $branchSlug,
                        ]) }}"
                            class="btn premium-btn btn-main-premium">
                            <i class="fas fa-plus"></i>
                            Add User
                        </a>
                    @elseif(!empty($restaurantSlug))
                        <a href="{{ route('restaurant.users.create', [
                            'restaurant' => $restaurantSlug,
                        ]) }}"
                            class="btn premium-btn btn-main-premium">
                            <i class="fas fa-plus"></i>
                            Add User
                        </a>
                    @endif
                @endcan
            </div>
        </section>
        <section class="section premium-dashboard pt-0">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card premium-block">
                            <div class="card-header premium-card-header">
                                <div>
                                    <h4 class="mb-1">All Users</h4>
                                    <p class="header-subtext mb-0"> User records from API </p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Restaurant Name</th>
                                                <th>Profile</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th width="220">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($users as $user)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td> {{ $user->restaurant?->name ?? '-' }}</td>
                                                    <td>

                                                        @if ($user->profile_photo)
                                                            <img src="{{ asset($user->profile_photo) }}" width="45"
                                                                height="45" style="border-radius:50%;object-fit:cover;">
                                                        @else
                                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user['name']) }}"
                                                                width="45" height="45" style="border-radius:50%;">
                                                        @endif
                                                    </td>
                                                    <td><strong>{{ $user['name'] }}</strong></td>
                                                    <td> {{ $user['email'] }} </td>
                                                    <td> {{ $user['phone'] ?? '-' }} </td>
                                                    <td> <span class="badge badge-primary">
                                                            {{ ucwords(str_replace('_', ' ', $user['role'] ?? 'No Role')) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if (($user['status'] ?? '') == 'active')
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-danger"> Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">

                                                            @php
                                                                $restaurantSlug = request()->route('restaurant');
                                                                $branchSlug = request()->route('branch');
                                                            @endphp


                                                            {{-- EDIT BUTTON --}}
                                                            @if (auth()->user()->role == 'super_admin')
                                                                <a href="{{ route('users.edit', $user->id) }}"
                                                                    class="btn btn-sm btn-primary">
                                                                    Edit
                                                                </a>
                                                            @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                                                                <a href="{{ route('branch.users.edit', [
                                                                    'restaurant' => $restaurantSlug,
                                                                    'branch' => $branchSlug,
                                                                    'user' => $user->id,
                                                                ]) }}"
                                                                    class="btn btn-sm btn-primary">
                                                                    Edit
                                                                </a>
                                                            @elseif(!empty($restaurantSlug))
                                                                <a href="{{ route('restaurant.users.edit', [
                                                                    'restaurant' => $restaurantSlug,
                                                                    'user' => $user->id,
                                                                ]) }}"
                                                                    class="btn btn-sm btn-primary">
                                                                    Edit
                                                                </a>
                                                            @endif



                                                            {{-- DELETE BUTTON --}}
                                                            @if (auth()->user()->role == 'super_admin')
                                                                <form action="{{ route('users.destroy', $user->id) }}"
                                                                    method="POST" class="delete-form">
                                                                @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                                                                    <form
                                                                        action="{{ route('branch.users.destroy', [
                                                                            'restaurant' => $restaurantSlug,
                                                                            'branch' => $branchSlug,
                                                                            'user' => $user->id,
                                                                        ]) }}"
                                                                        method="POST" class="delete-form">
                                                                    @elseif(!empty($restaurantSlug))
                                                                        <form
                                                                            action="{{ route('restaurant.users.destroy', [
                                                                                'restaurant' => $restaurantSlug,
                                                                                'user' => $user->id,
                                                                            ]) }}"
                                                                            method="POST" class="delete-form">
                                                            @endif

                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>

                                                            </form>

                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        No Users Found
                                                    </td>
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
                            title: 'Delete User?',
                            text: 'This action cannot be undone.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes Delete',
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
