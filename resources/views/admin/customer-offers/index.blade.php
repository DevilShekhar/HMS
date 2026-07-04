@can('create-customer-offers')
    @extends('layouts.app') 
    @section('content')
        <section class="section premium-dashboard">
            <div class="premium-floating-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                          <i class="fas fa-bullhorn"></i>
                        </div>
                        <div>
                            <span class="header-badge">
                                 Customer Offer Management
                            </span>
                            <h1>Create Customer Offer</h1>
                            <p>Create attractive offers and promotions for your customers.</p>
                        </div>
                    </div>
                    <div class="header-right">
                        @php
                            $restaurantSlug = auth()->user()->restaurant?->slug;
                            $branchSlug = auth()->user()->branch?->slug;
                        @endphp
                        <div class="premium-head-actions">
                            @can('create-customer-offers')
                                @if ($branchSlug)
                                    <a href="{{ route('branch.customer-offers.create', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug,]) }}" class="premium-back-btn">
                                        <i class="fas fa-plus"></i>Add Customer Offer
                                    </a>
                                @else
                                    <a href="{{ route('restaurant.customer-offers.create', ['restaurant' => $restaurantSlug,]) }}" class="premium-back-btn">
                                        <i class="fas fa-plus"></i>Add Customer Offer
                                    </a>
                                @endif
                            @endcan
                        </div>                
                    </div>
                </div>    
            </div>    
        </section>       
        <section class="section premium-dashboard pt-0">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card premium-block">                       
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="tableExport">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Category</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                <th width="220">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($offers as $offer)
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
                                                    <td class="text-center">
                                                        <div class="action-buttons">
                                                            @php
                                                                $restaurantSlug = auth()->user()->restaurant?->slug;
                                                                $branchSlug = auth()->user()->branch?->slug;
                                                            @endphp
                                                            @if ($branchSlug)
                                                                <a href="{{ route('branch.customer-offers.edit', ['restaurant' => $restaurantSlug,'branch' => $branchSlug,'customerOffer' => $offer->id,]) }}" class="btn btn-md btn-warning">  <i class="fas fa-pen"></i></a>
                                                                @else
                                                                    <a href="{{ route('restaurant.customer-offers.edit', ['restaurant' => $restaurantSlug,'customerOffer' => $offer->id,]) }}" class="btn btn-md btn-warning"> <i class="fas fa-pen"></i></a>
                                                                @endif
                                                                @if ($branchSlug)
                                                                    <a href="{{ route('branch.customer-offers.show', ['restaurant' => $restaurantSlug,'branch' => $branchSlug,'customerOffer' => $offer->id,]) }}" class="btn btn-md btn-info"> <i class="fas fa-eye"></i></a>
                                                                @else
                                                                    <a href="{{ route('restaurant.customer-offers.show', ['restaurant' => $restaurantSlug, 'customerOffer' => $offer->id,]) }}" class="btn btn-md btn-info"> <i class="fas fa-eye"></i></a>
                                                                @endif
                                                                @if ($branchSlug)
                                                                    <form action="{{ route('branch.customer-offers.destroy', ['restaurant' => $restaurantSlug,'branch' => $branchSlug,'customerOffer' => $offer->id,]) }}" method="POST" class=" delete-form">
                                                                @else
                                                                    <form action="{{ route('restaurant.customer-offers.destroy', ['restaurant' => $restaurantSlug,'customerOffer' => $offer->id,]) }}" method="POST" class=" delete-form">
                                                                @endif
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-md btn-danger">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                                </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                 @empty
                                                <tr>
                                                    <td colspan="6" class="text-center"> No Restaurants Found </td>
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
