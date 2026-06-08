@extends('layouts.app')
@section('content')
<section class="section premium-dashboard">
    <div class="premium-page-head">
        <div class="premium-page-title">
            <span class="mini-badge">Branch Management</span>
            <h2>Branches</h2>
            <p>Manage restaurant branches.</p>
        </div>
        <div class="premium-head-actions">
            @if(auth()->user()->role == 'super_admin')
                <a href="{{ route('branches.create') }}"
                   class="btn premium-btn">
                    <i class="fas fa-plus"></i>
                    Add Branch
                </a>
            @endif
        </div>
    </div>
</section>
<section class="section premium-dashboard pt-0">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card premium-block">
        <div class="card-header premium-card-header">
            <div>
                <h4>Branch List</h4>
                <p class="header-subtext">
                    View and manage all branches.
                </p>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Branch</th>
                            <th>Restaurant</th>
                            <th>Owner</th>
                            <th>Manager</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Status</th>
                            <th width="220">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                            <tr>
                                <td>{{ $loop->iteration + (($branches->currentPage()-1) * $branches->perPage()) }}</td>
                                <td>
                                    <strong>{{ $branch->name }}</strong>

                                    @if($branch->code)
                                        <br>
                                        <small class="text-muted">
                                            {{ $branch->code }}
                                        </small>
                                    @endif
                                </td>
                                <td>{{ optional($branch->restaurant)->name }} </td>
                                <td> {{ optional($branch->owner)->name }} </td>
                                <td>
                                    @if($branch->manager)
                                        <span class="badge bg-success">
                                            {{ $branch->manager->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            Not Assigned
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $branch->phone }}</td>
                                <td>{{ $branch->city }}</td>
                                <td>
                                    @if($branch->is_active)
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
                                    <div class="d-flex gap-1">
                                        @if(auth()->user()->role == 'super_admin')
                                            <a href="{{ route('branches.show',$branch->id) }}"
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('branches.edit',$branch->id) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('branches.destroy',$branch->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('restaurant.branches.show',[
                                                'restaurant' => request()->route('restaurant'),
                                                'branch' => $branch->id
                                            ]) }}"
                                               class="btn btn-info btn-sm ">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-primary btn-sm assign-manager-btn"
                                                data-toggle="modal"
                                                data-target="#assignManagerModal"
                                                data-url="{{ route('restaurant.branches.assign-manager', [
                                                    'restaurant' => request()->route('restaurant'),
                                                    'branch' => $branch->id
                                                ]) }}">
                                                <i class="fas fa-user-tie"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    No branches found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($branches->hasPages())
            <div class="card-footer">
                {{ $branches->links() }}
            </div>
        @endif
    </div>
</section>
@if(auth()->user()->role == 'owner')
<div class="modal fade"  id="assignManagerModal" tabindex="-1"  role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="assignManagerForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        Assign Branch Manager
                    </h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Branch Manager</label>
                        <select name="branch_manager_id" class="form-control"  required>
                            <option value="">  Select Manager</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}">
                                    {{ $manager->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-primary"> Assign Manager </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
    @if(auth()->user()->role == 'owner')
        @push('scripts')
        <script>
        $(document).ready(function () {
            $('.assign-manager-btn').on('click', function () {
                let actionUrl = $(this).data('url');
                console.log('Assign URL:', actionUrl);
                $('#assignManagerForm').attr('action', actionUrl);
            });
        });
        </script>
        @endpush
    @endif
@endsection