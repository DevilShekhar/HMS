@extends('layouts.app')
@section('content')
    <div class="eht-container">
        <div class="eht-header">
            <div class="eht-page-title">
                <span class="eht-badge">User Management</span>
                <h1>Edit User</h1>
                <p>Update user account details and role permissions.</p>
            </div>
            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

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
                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" id="userForm">
            @else
                <form action="{{ route('restaurant.users.update', ['restaurant' => auth()->user()->restaurant->slug, 'user' => $user->id]) }}" method="POST" enctype="multipart/form-data">
            @endif
                @csrf
                @method('PUT')

                <div class="eht-main-card">
                    <div class="eht-card-body">
                        <div class="eht-form-grid">

                            <div class="eht-left-form">
                                <h3 class="section-title">User Information</h3>

                                <div class="eht-fields-grid">
                                    <div class="eht-field">
                                        <label>Full Name <span class="required">*</span></label>
                                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="eht-input @error('name') is-invalid @enderror">
                                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="eht-field">
                                        <label>Email Address</label>
                                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="eht-input @error('email') is-invalid @enderror">
                                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="eht-field">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="eht-input @error('phone') is-invalid @enderror">
                                        @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="eht-field">
                                        <label>Gender</label>
                                        <select name="gender" class="eht-input">
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="eht-field">
                                        <label>Birth Date</label>
                                        <input type="date" name="birth_date" value="{{ old('birth_date', optional($user->birth_date)->format('Y-m-d')) }}" class="eht-input @error('birth_date') is-invalid @enderror">
                                        @error('birth_date') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    @if (auth()->user()->role == 'super_admin')
                                    <div class="eht-field">
                                        <label>Restaurant</label>
                                        <select name="restaurant_id" class="eht-input">
                                            <option value="">Select Restaurant</option>
                                            @foreach ($restaurants as $restaurant)
                                                <option value="{{ $restaurant->id }}" {{ old('restaurant_id', $user->restaurant_id) == $restaurant->id ? 'selected' : '' }}>
                                                    {{ $restaurant->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('restaurant_id') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    @endif

                                    <div class="eht-field">
                                        <label>Status</label>
                                        <select name="status" class="eht-input">
                                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="eht-field">
                                        <label>Role</label>
                                        <select name="role" class="eht-input">
                                            <option value="">Select Role</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace('_', ' ', $role)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="eht-field full-width">
                                        <label>Address</label>
                                        <textarea name="address" rows="6" class="eht-input">{{ old('address', $user->address) }}</textarea>
                                        @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="eht-profile-section">
                                <h3 class="section-title">Profile Photo</h3>
                                <div class="profile-upload-area">
                                    @if ($user->profile_photo)
                                        <img src="{{ asset('storage/' . $user->profile_photo) }}" class="eht-profile-img" alt="{{ $user->name }}">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}" class="eht-profile-img" alt="{{ $user->name }}">
                                    @endif
                                    <input type="file" name="profile_photo" class="eht-file-input" accept="image/*">
                                    @error('profile_photo') <small class="text-danger d-block mt-2">{{ $message }}</small> @enderror
                                    <small class="eht-help-text mt-2">JPG, PNG, JPEG Supported (Max 2MB)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="eht-form-footer">
                        <button type="submit" class="eht-btn eht-btn-primary eht-btn-large">
                            Update User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
