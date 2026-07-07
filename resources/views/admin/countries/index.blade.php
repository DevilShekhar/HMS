@can('view-country')
    @extends('layouts.app')

    @section('content')
        <section class="section premium-dashboard">
            <div class="premium-floating-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-globe-asia"></i>
                        </div>
                        <div>
                            <span class="header-badge">Country Management</span>
                            <h1>Countries</h1>
                            <p>Manage countries, currencies and timezones.</p>
                        </div>
                    </div>

                    <div class="header-right">
                        <a href="{{ route('countries.create') }}" class="premium-back-btn">
                            <i class="fas fa-plus"></i> Add Country
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="section premium-dashboard pt-0">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card premium-block">
                <div class="card-header premium-card-header">
                    <div>
                        <h4>Country List</h4>
                        <p class="header-subtext">View and manage all countries.</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tableExport">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Country</th>
                                    <th>ISO Code</th>
                                    <th>Currency</th>
                                    <th>Symbol</th>
                                    <th>Timezone</th>
                                    <th>Status</th>
                                    <th width="170">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($countries as $country)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration + ($countries->currentPage() - 1) * $countries->perPage() }}
                                        </td>
                                        <td>
                                            <strong>{{ $country->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary text-white">{{ $country->iso_code }}</span>
                                        </td>
                                        <td>{{ $country->currency_code }}</td>
                                        <td>{{ $country->currency_symbol }}</td>
                                        <td>{{ $country->timezone }}</td>
                                        <td>
                                            @if ($country->status)
                                                <span class="status active">
                                                    <i class="fas fa-circle"></i> Active
                                                </span>
                                            @else
                                                <span class="status inactive">
                                                    <i class="fas fa-circle"></i> Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('countries.edit', $country->id) }}"
                                                    class="btn btn-warning btn-md">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('countries.destroy', $country->id) }}" method="POST"
                                                    class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-md">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No countries found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $countries->links() }}
                    </div>
                </div>
            </div>
        </section>

    @endsection

    @push('scripts')
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
                            title: 'Delete Country?',
                            text: 'This action cannot be undone.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Delete',
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
    @endpush
@else

    @php
        abort(403);
    @endphp

@endcan
