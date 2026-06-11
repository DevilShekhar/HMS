@extends('layouts.app')

@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <h2>Create Role</h2>
            </div>
        </div>
    </section>

    <section class="section premium-dashboard pt-0">

       <form method="POST" action="{{ route('roles.store') }}">
    @csrf

            <div class="card premium-block">
                <div class="card-body">

                    <div class="mb-3">
                        <label>Role Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>

                    <button class="btn btn-success">Save</button>

                </div>
            </div>

        </form>

    </section>
@endsection
