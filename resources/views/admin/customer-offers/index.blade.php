@can('create-customer-offers')
    @extends('layouts.app')

    @section('content')
        <div class="card">

            <div class="card-header d-flex justify-content-between">
                <h4>Customer Offers</h4>

                @php
                    $restaurantSlug = auth()->user()->restaurant?->slug;
                    $branchSlug = auth()->user()->branch?->slug;
                @endphp

                <div class="premium-head-actions">

                    @can('create-customer-offers')
                        @if ($branchSlug)
                            <a href="{{ route('branch.customer-offers.create', [
                                'restaurant' => $restaurantSlug,
                                'branch' => $branchSlug,
                            ]) }}"
                                class="btn btn-primary">

                                <i class="fas fa-plus"></i>
                                Add Customer Offer

                            </a>
                        @else
                            <a href="{{ route('restaurant.customer-offers.create', [
                                'restaurant' => $restaurantSlug,
                            ]) }}"
                                class="btn btn-primary">

                                <i class="fas fa-plus"></i>
                                Add Customer Offer

                            </a>
                        @endif
                    @endcan

                </div>
            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($offers as $offer)
                            <tr>

                                <td>{{ $offer->id }}</td>
                                <td>{{ $offer->category }}</td>
                                <td>{{ $offer->title }}</td>
                                <td>
                                    {{ \Illuminate\Support\Str::limit(trim(html_entity_decode(strip_tags($offer->description))), 20) }}
                                </td>

                                <td>
                                    @if ($offer->status)
                                        <span class="badge bg-success">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td>

                                    @php
                                        $restaurantSlug = auth()->user()->restaurant?->slug;
                                        $branchSlug = auth()->user()->branch?->slug;
                                    @endphp


                                    {{-- EDIT --}}
                                    @if ($branchSlug)
                                        <a href="{{ route('branch.customer-offers.edit', [
                                            'restaurant' => $restaurantSlug,
                                            'branch' => $branchSlug,
                                            'customerOffer' => $offer->id,
                                        ]) }}"
                                            class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                    @else
                                        <a href="{{ route('restaurant.customer-offers.edit', [
                                            'restaurant' => $restaurantSlug,
                                            'customerOffer' => $offer->id,
                                        ]) }}"
                                            class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                    @endif



                                    {{-- VIEW --}}
                                    @if ($branchSlug)
                                        <a href="{{ route('branch.customer-offers.show', [
                                            'restaurant' => $restaurantSlug,
                                            'branch' => $branchSlug,
                                            'customerOffer' => $offer->id,
                                        ]) }}"
                                            class="btn btn-sm btn-info">
                                            View
                                        </a>
                                    @else
                                        <a href="{{ route('restaurant.customer-offers.show', [
                                            'restaurant' => $restaurantSlug,
                                            'customerOffer' => $offer->id,
                                        ]) }}"
                                            class="btn btn-sm btn-info">
                                            View
                                        </a>
                                    @endif



                                    {{-- DELETE --}}
                                    @if ($branchSlug)
                                        <form
                                            action="{{ route('branch.customer-offers.destroy', [
                                                'restaurant' => $restaurantSlug,
                                                'branch' => $branchSlug,
                                                'customerOffer' => $offer->id,
                                            ]) }}"
                                            method="POST" class="d-inline delete-form">
                                        @else
                                            <form
                                                action="{{ route('restaurant.customer-offers.destroy', [
                                                    'restaurant' => $restaurantSlug,
                                                    'customerOffer' => $offer->id,
                                                ]) }}"
                                                method="POST" class="d-inline delete-form">
                                    @endif


                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Delete
                                    </button>

                                    </form>

                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>

                {{ $offers->links() }}

            </div>
        </div>
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
@else
    @php
        abort(403);
    @endphp
@endcan
