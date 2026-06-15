@can('view-permission')
    @extends('layouts.app')

    @section('content')
        <section class="section premium-dashboard">
            <div class="premium-page-head">
                <div class="premium-page-title">
                    <span class="mini-badge">Permission Management</span>
                    <h2>Permissions</h2>
                    <p>Manage role-based permissions</p>
                </div>

                <div class="premium-head-actions">
                    <a href="{{ route('permissions.create') }}" class="btn premium-btn btn-main-premium">Add Permission</a>
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
                                    <h4 class="mb-1">All Permissions</h4>
                                    <p class="header-subtext mb-0">
                                        Role-based access control system
                                    </p>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="permissionsTable">

                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Permission</th>
                                                <th width="200">Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse($permissions as $permission)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>

                                                    <td>
                                                        <strong>{{ $permission->name }}</strong>
                                                    </td>

                                                    <td>
                                                        <div class="d-flex">

                                                            <a href="{{ route('permissions.edit', [$permission->id]) }}"
                                                                class="btn btn-sm btn-primary mr-2">
                                                                Edit
                                                            </a>

                                                            <form action="{{ route('permissions.destroy', [$permission->id]) }}"
                                                                method="POST" class="delete-form">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    Delete
                                                                </button>
                                                            </form>

                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        No Permissions Found
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
            <script>
                $(function() {
                    $('#permissionsTable').DataTable({
                        responsive: false,
                        autoWidth: false
                    });
                });
            </script>

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
