@can('view-user')
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
                            <span class="header-badge">
                               User List
                            </span>
                            <p>Manage all users from API server</p>
                        </div>
                    </div>
                    @php
                        $restaurantSlug = request()->route('restaurant');
                        $branchSlug = request()->route('branch');
                    @endphp
                    <div class="header-right">
                        @if (auth()->user()->role === 'super_admin')
                            <a href="{{ route('users.create') }}" class="premium-back-btn">
                                <i class="fas fa-plus"></i>
                                Add User
                            </a>
                        @elseif (!empty($restaurantSlug) && !empty($branchSlug))
                                    <a href="{{ route('branch.users.create', [
                                'restaurant' => $restaurantSlug,
                                'branch' => $branchSlug,
                            ]) }}" class="premium-back-btn">
                                        <i class="fas fa-plus"></i>
                                        Add User
                                    </a>
                        @elseif (!empty($restaurantSlug))
                                    <a href="{{ route('restaurant.users.create', [
                                'restaurant' => $restaurantSlug,
                            ]) }}" class="premium-back-btn">
                                        <i class="fas fa-plus"></i>
                                        Add User
                                    </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        <div class="eht-container">
            <div class="eht-main-card">
                <div class="eht-card-header">
                    <div>
                        <h3><i class="fas fa-list" style="color: #FA5603;"></i> All Users</h3>
                        <p class="header-subtext">User records from API</p>
                    </div>
                    <span class="total-badge">
                        Total: {{ $users->total() }}
                    </span>
                </div>

                <div class="eht-card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover eht-table" id="tableExport">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Restaurant</th>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th width="180">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->restaurant?->name ?? '-' }}</td>
                                        <td>
                                            @if ($user->profile_photo)
                                                <img src="{{ asset($user->profile_photo) }}" class="eht-profile-img" alt="Profile">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=FA5603&color=fff"
                                                    class="eht-profile-img" alt="Avatar">
                                            @endif
                                        </td>
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone ?? '-' }}</td>
                                        <td>
                                            <span class="badge eht-role-badge">
                                                {{ ucwords(str_replace('_', ' ', $user->role ?? 'No Role')) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if (($user->status ?? '') == 'active')
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown eht-action-dropdown">
                                                <button class="btn btn-sm btn-link text-secondary p-0" type="button"
                                                    id="dropdownMenuUser{{ $user->id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false" style="width: 30px; height: 30px; line-height: 30px;">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0"
                                                    aria-labelledby="dropdownMenuUser{{ $user->id }}"
                                                    style="border-radius: 8px; font-size: 0.85rem;">
                                                    @php
                                                        $restaurantSlug = request()->route('restaurant');
                                                        $branchSlug = request()->route('branch');
                                                    @endphp

                                                    {{-- Edit Option --}}
                                                    <li>
                                                        @if (auth()->user()->role == 'super_admin')
                                                            <a class="dropdown-item py-2" href="{{ route('users.edit', $user->id) }}">
                                                                <i class="fas fa-edit me-2" style="color: #FA5603; width: 16px;"></i>
                                                                Edit User
                                                            </a>
                                                        @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                                                            <a class="dropdown-item py-2"
                                                                href="{{ route('branch.users.edit', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug, 'user' => $user->id]) }}">
                                                                <i class="fas fa-edit me-2" style="color: #FA5603; width: 16px;"></i>
                                                                Edit User
                                                            </a>
                                                        @elseif(!empty($restaurantSlug))
                                                            <a class="dropdown-item py-2"
                                                                href="{{ route('restaurant.users.edit', ['restaurant' => $restaurantSlug, 'user' => $user->id]) }}">
                                                                <i class="fas fa-edit me-2" style="color: #FA5603; width: 16px;"></i>
                                                                Edit User
                                                            </a>
                                                        @endif
                                                    </li>

                                                    {{-- Divider line inside menu --}}
                                                    <li>
                                                        <hr class="dropdown-divider opacity-50">
                                                    </li>

                                                    {{-- Delete Option --}}
                                                    <li>
                                                        @if (auth()->user()->role == 'super_admin')
                                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                                class="delete-form m-0">
                                                        @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                                                                <form
                                                                    action="{{ route('branch.users.destroy', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug, 'user' => $user->id]) }}"
                                                                    method="POST" class="delete-form m-0">
                                                            @elseif(!empty($restaurantSlug))
                                                                    <form
                                                                        action="{{ route('restaurant.users.destroy', ['restaurant' => $restaurantSlug, 'user' => $user->id]) }}"
                                                                        method="POST" class="delete-form m-0">
                                                                @endif
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger py-2">
                                                                        <i class="fas fa-trash me-2" style="width: 16px;"></i>
                                                                        Delete User
                                                                    </button>
                                                                </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="eht-empty">
                                            <i class="fas fa-users"></i>
                                            <strong>No Users Found</strong>
                                            <p style="margin: 0.3rem 0 0; font-size: 0.9rem;">Start by adding your first user.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white border-top-0 px-4 pb-4">
                    {{ $users->links('pagination') }}
                </div>
            </div>
        </div>

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
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Delete User?',
                            text: 'This action cannot be undone.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Delete',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#FA5603'
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
    @php abort(403); @endphp
@endcan
