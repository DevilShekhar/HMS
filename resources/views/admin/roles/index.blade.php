@can('view-role')
    @extends('layouts.app')

    @section('content')
        @can('view-role')
            <section class="section premium-dashboard">
                <div class="premium-page-head">
                    <div class="premium-page-title">
                        <span class="mini-badge">Role Management</span>
                        <h2>Roles</h2>
                    </div>

                    <div class="premium-head-actions">
                        <a href="{{ route('roles.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Role
                        </a>
                    </div>
                </div>
            </section>

            <section class="section premium-dashboard pt-0">

                <div class="card premium-block">
                    <div class="card-body">

                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Role Name</th>
                                    <th>Status</th>
                                    <th width="250">Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($roles as $roleItem)
                                    {{-- ROLE ROW --}}
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <td>
                                            <strong>
                                                {{ ucwords(str_replace('_', ' ', $roleItem->name)) }}
                                            </strong>
                                        </td>
                                        <td>
                                            @if ($roleItem->status == 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($roleItem->status == 1)
                                            <a href="{{ route('roles.permissions', $roleItem->id) }}"
                                                class="btn btn-sm btn-primary">
                                                Manage Permission
                                            </a>


                                            <form action="{{ route('roles.destroy', $roleItem->id) }}" method="POST"
                                                class="delete-form" style="display:inline;">
                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-danger">
                                                    Delete
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- INLINE PERMISSION PANEL --}}
                                    @if (isset($role) && $role->id == $roleItem->id)
                                        <tr>
                                            <td colspan="3">

                                                <form method="POST"
                                                    action="{{ route('roles.permissions.update', [
                                                        'restaurant' => $restaurant->slug,
                                                        'role' => $role->id,
                                                    ]) }}">

                                                    @csrf

                                                    <div class="row p-3 border rounded bg-light">

                                                        @foreach ($permissions as $permission)
                                                            <div class="col-md-3 mb-2">
                                                                <label>
                                                                    <input type="checkbox" name="permissions[]"
                                                                        value="{{ $permission->name }}"
                                                                        {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                                                    {{ $permission->name }}
                                                                </label>
                                                            </div>
                                                        @endforeach

                                                        <div class="col-12 mt-3">
                                                            <button class="btn btn-success btn-sm">
                                                                Save Permissions
                                                            </button>
                                                        </div>

                                                    </div>

                                                </form>

                                            </td>
                                        </tr>
                                    @endif

                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            No Roles Found
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>
                </div>

            </section>
            <div class="modal fade" id="permissionModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                Manage Permissions - <span id="roleName"></span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form id="permissionForm">
                            @csrf

                            <div class="modal-body">
                                <div class="row" id="permissionList">
                                    {{-- permissions load here --}}
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">
                                    Save Permissions
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

            <script>
                function openPermissionModal(roleId) {

                    $.ajax({
                        url: "/demo/roles/" + roleId + "/permissions-data",
                        type: "GET",
                        success: function(response) {

                            $('#roleName').text(response.role.name);

                            let html = '';

                            response.permissions.forEach(function(permission) {

                                let checked = response.rolePermissions.includes(permission.name) ?
                                    'checked' :
                                    '';

                                html += `
                                <div class="col-md-4 mb-2">
                                    <label>
                                        <input type="checkbox"
                                            name="permissions[]"
                                            value="${permission.name}"
                                            ${checked}>
                                        ${permission.name}
                                    </label>
                                </div>
                            `;
                            });

                            $('#permissionList').html(html);

                            $('#permissionForm').data('role-id', roleId);

                            $('#permissionModal').modal('show');
                        }
                    });
                }


                // SAVE PERMISSIONS
                $('#permissionForm').submit(function(e) {
                    e.preventDefault();

                    let roleId = $(this).data('role-id');

                    $.ajax({
                        url: "/demo/roles/" + roleId + "/permissions",
                        type: "POST",
                        data: $(this).serialize(),
                        success: function(res) {
                            alert('Permissions updated');
                            $('#permissionModal').modal('hide');
                        }
                    });
                });
            </script>
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
        @endcan
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
@else
    @php
        abort(403);
    @endphp
@endcan
