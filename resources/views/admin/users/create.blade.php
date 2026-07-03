@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">User Management</span>
                <h2>Create User</h2>
                <p>Add a new user account to the system.</p>
            </div>
            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

            <div class="premium-head-actions">

                @if (auth()->user()->role === 'super_admin')
                    <a href="{{ route('users.index') }}" class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Users
                    </a>
                @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                    <a href="{{ route('branch.users.index', [
                        'restaurant' => $restaurantSlug,
                        'branch' => $branchSlug,
                    ]) }}"
                        class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Users
                    </a>
                @elseif(!empty($restaurantSlug))
                    <a href="{{ route('restaurant.users.index', [
                        'restaurant' => $restaurantSlug,
                    ]) }}"
                        class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Users
                    </a>
                @endif

            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        @if (auth()->user()->role == 'super_admin')
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
            @else
                <form action="{{ route('restaurant.users.store', ['restaurant' => $restaurant]) }}"method="POST"
                    enctype="multipart/form-data">
        @endif
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card premium-block">
                    <div class="card-header premium-card-header">
                        <div>
                            <h4>User Information</h4>
                            <p class="header-subtext"> Enter user account details. </p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">
                                    Full Name <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="form-control premium-input @error('name') is-invalid @enderror"
                                    >

                                @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Email Address</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="form-control premium-input" >
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control premium-input" >
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control premium-input" >
                                @error('password_confirmation')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                    class="form-control premium-input" >
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Gender</label>
                                <select name="gender" class="form-control premium-input" required>
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('gender')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Birth Date</label>
                                <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                                    class="form-control premium-input" >
                                @error('birth_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            @if (auth()->user()->role == 'super_admin')
                                <div class="col-md-6 mb-4">
                                    <label>Restaurant</label>
                                    <select name="restaurant_id" class="form-control premium-input">
                                        <option value="">Select Restaurant</option>
                                        @foreach ($restaurants as $restaurant)
                                            <option value="{{ $restaurant->id }}"
                                                {{ old('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                                                {{ $restaurant->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('restaurant_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            @endif
                            <!-- Branch Selection with Search -->
                            @if (auth()->user()->role == 'super_admin' || auth()->user()->role == 'owner')
                                <div class="col-md-6 mb-4">
                                    <label>Branch <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_id" class="form-control premium-input">
                                        <option value="">Select Branch</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            @else
                                <!-- Auto assign for Branch Manager -->
                                <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id ?? '' }}">
                            @endif
                            <div class="col-md-6 mb-4">
                                <label>Status</label>
                                <select name="status" class="form-control premium-input">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Role</label>
                                <select name="role" class="form-control premium-input">
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $role)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-4">
                                <label>Address</label>
                                <textarea name="address" rows="4" class="form-control premium-input" >{{ old('address') }}</textarea>
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"> Create User </button>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card premium-block">
                    <div class="card-header premium-card-header">
                        <div>
                            <h4>Profile Photo</h4>
                            <p class="header-subtext">
                                Upload profile image.
                            </p>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/img/user.png') }}" class="rounded-circle mb-3" width="120"
                            height="120">
                        <input type="file" name="profile_photo" class="form-control premium-input">
                        @error('profile_photo')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        <small class="text-muted mt-2 d-block">
                            JPG, PNG, JPEG Supported
                        </small>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </section>


    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#branch_id').select2({
                    placeholder: "Select and search branch",
                    allowClear: true,
                    width: '100%'
                });
            });
        </script>
    @endpush
@endsection
