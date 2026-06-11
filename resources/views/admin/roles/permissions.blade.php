@extends('layouts.app')

@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Role Management</span>
                <h2>Manage Permissions</h2>
                <p>Role: <b>{{ $role->name }}</b></p>
            </div>
        </div>
    </section>

    <section class="section premium-dashboard pt-0">

        <form method="POST"
            action="{{ route('roles.permissions.update', [

                'role' => $role->id,
            ]) }}">

            @csrf

            <div class="card premium-block">
                <div class="card-header premium-card-header">
                    <h4>All Permissions</h4>
                </div>

                <div class="card-body">

                    <div class="row">

                        @foreach ($permissions as $groupName => $groupPermissions)
                            @if ($groupPermissions->count() > 0)
                                <div class="col-12 mb-4">

                                    <div class="card border">

                                        <div class="card-header bg-light">
                                            <strong>{{ $groupName }}</strong>
                                        </div>

                                        <div class="card-body">

                                            <div class="row">

                                                @foreach ($groupPermissions as $permission)
                                                    <div class="col-md-4 mb-2">

                                                        <label class="d-flex align-items-center gap-2">

                                                            <input type="checkbox" name="permissions[]"
                                                                value="{{ $permission->name }}"
                                                                {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>

                                                            <span>
                                                                {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                            </span>

                                                        </label>

                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>

                                    </div>

                                </div>
                            @endif
                        @endforeach

                    </div>

                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-success">
                    Save Permissions
                </button>

                {{-- <a href="{{ route('roles.index', ['restaurant' => $restaurant->slug]) }}" class="btn btn-secondary">
                    Back
                </a> --}}
            </div>

        </form>

    </section>
@endsection
