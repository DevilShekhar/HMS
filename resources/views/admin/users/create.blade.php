@extends('layouts.app')

@section('content')
    <div class="eht-container">
        <!-- Header -->
        <div class="eht-header">
            <div class="eht-page-title">
                <span class="eht-badge">User Management</span>
                <h1>Create New User</h1>
                <p>Add a new user account to the system</p>
            </div>

            <div class="eht-back-btn">
                @if (auth()->user()->role === 'super_admin')
                    <a href="{{ route('users.index') }}" class="eht-btn eht-btn-ghost">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                    <a href="{{ route('branch.users.index', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug]) }}"
                       class="eht-btn eht-btn-ghost">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                @elseif(!empty($restaurantSlug))
                    <a href="{{ route('restaurant.users.index', ['restaurant' => $restaurantSlug]) }}"
                       class="eht-btn eht-btn-ghost">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                @endif
            </div>
        </div>

        <div class="eht-form-wrapper">
            @if (auth()->user()->role == 'super_admin')
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
            @else
                <form action="{{ route('restaurant.users.store', ['restaurant' => $restaurant]) }}" method="POST" enctype="multipart/form-data">
            @endif
                @csrf

                <div class="eht-main-card">
                    <div class="eht-card-body">
                        <div class="eht-form-grid">

                            <!-- Left: Form Fields (4 per row) -->
                            <div class="eht-left-form">
                                <h3 class="section-title">User Information</h3>

                                <div class="eht-fields-grid">
                                    <!-- Row 1 -->
                                    <div class="eht-field">
                                        <label>Full Name <span class="required">*</span></label>
                                        <input type="text" name="name" value="{{ old('name') }}" class="eht-input @error('name') is-invalid @enderror">
                                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="eht-field">
                                        <label>Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}" class="eht-input @error('email') is-invalid @enderror">
                                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="eht-field">
                                        <label>Password</label>
                                        <input type="password" name="password" class="eht-input @error('password') is-invalid @enderror">
                                        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="eht-field">
                                        <label>Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="eht-input">
                                        @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- Row 2 -->
                                    <div class="eht-field">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone" value="{{ old('phone') }}" class="eht-input @error('phone') is-invalid @enderror">
                                        @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="eht-field">
                                        <label>Gender</label>
                                        <select name="gender" class="eht-input" required>
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                        @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="eht-field">
                                        <label>Birth Date</label>
                                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="eht-input @error('birth_date') is-invalid @enderror">
                                        @error('birth_date') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- Row 3 -->
                                    @if (auth()->user()->role == 'super_admin')
                                    <div class="eht-field">
                                        <label>Restaurant</label>
                                        <select name="restaurant_id" class="eht-input">
                                            <option value="">Select Restaurant</option>
                                            @foreach ($restaurants as $restaurant)
                                                <option value="{{ $restaurant->id }}" {{ old('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                                                    {{ $restaurant->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                    @if (auth()->user()->role != 'super_admin' || auth()->user()->role == 'owner')
                                    <div class="eht-field">
                                        <label>Branch <span class="required">*</span></label>
                                        <select name="branch_id" id="branch_id" class="eht-input">
                                            <option value="">Select Branch</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    @else
                                        <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id ?? '' }}">
                                    @endif

                                    <div class="eht-field">
                                        <label>Role</label>
                                        <select name="role" class="eht-input">
                                            <option value="">Select Role</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace('_', ' ', $role)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- Address - Full Width -->
                                    <div class="eht-field full-width">
                                        <label>Address</label>
                                        <textarea name="address" rows="6" class="eht-input">{{ old('address') }}</textarea>
                                        @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Profile Photo -->
                            <div class="eht-profile-section">
                                <h3 class="section-title">Profile Photo</h3>
                                <div class="profile-upload-area">
                                    <img src="{{ asset('assets/img/user.png') }}" class="eht-profile-img" alt="Profile">
                                    <input type="file" name="profile_photo" class="eht-file-input" accept="image/*">
                                    @error('profile_photo') <small class="text-danger">{{ $message }}</small> @enderror
                                    <small class="eht-help-text">JPG, PNG, JPEG Supported</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="eht-form-footer">
                        <button type="submit" class="eht-btn eht-btn-primary eht-btn-large">
                            Create User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
