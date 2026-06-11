@extends('layouts.app')

@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Permission Management</span>
                <h2>Create Permission</h2>
                <p>Add new permission and assign roles</p>
            </div>

            <div class="premium-head-actions">
                <a href="{{ route('permissions.index') }}"
                    class="btn premium-btn ghost-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back To Permissions
                </a>
            </div>
        </div>
    </section>

    <section class="section premium-dashboard pt-0">

        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf

            <div class="row">

                <!-- LEFT SIDE -->
                <div class="col-lg-8">

                    <div class="card premium-block">
                        <div class="card-header premium-card-header">
                            <div>
                                <h4>Permission Information</h4>
                                <p class="header-subtext">Define permission name and assign roles</p>
                            </div>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                
                                <!-- Permission Name -->
                                <div class="col-md-12 mb-4">
                                    <label>Permission Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                        class="form-control premium-input" placeholder="e.g. create-order">

                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>



                            </div>

                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('permissions.index') }}"
                            class="btn btn-light">
                            Cancel
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Create Permission
                        </button>
                    </div>

                </div>

            </div>

        </form>

    </section>
@endsection
