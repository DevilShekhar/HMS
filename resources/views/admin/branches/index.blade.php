@can('view-branch')
    @extends('layouts.app')
    @section('content')
        <section class="section premium-dashboard">
            <div class="premium-floating-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-code-branch"></i>
                        </div>
                        <div>
                            <span class="header-badge">
                                Branch Management
                            </span>
                            <h1>Branches</h1>
                            <p>Manage restaurant branches.</p>
                        </div>
                    </div>

                    <div class="header-right">
                        @if (auth()->user()->role == 'super_admin')
                            <a href="{{ route('branches.create') }}" class="premium-back-btn">
                                <i class="fas fa-plus"></i>
                                Add Branch
                            </a>
                        @endif
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
                        <h4>Branch List</h4>
                        <p class="header-subtext">View and manage all branches.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tableExport">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Branch</th>
                                    <th>Restaurant</th>
                                    <th>Owner</th>
                                    <th>Manager</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>QR Code</th>
                                    <th>Expiry Date</th>
                                    <th>Status</th>
                                    <th width="250">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branches as $branch)
                                    <tr>
                                        <td>{{ $loop->iteration + ($branches->currentPage() - 1) * $branches->perPage() }}</td>
                                        <td>
                                            <strong>{{ $branch->name }}</strong>
                                            @if ($branch->code)
                                                <br><small class="text-muted">{{ $branch->code }}</small>
                                            @endif
                                        </td>
                                        <td>{{ optional($branch->restaurant)->name }}</td>
                                        <td>{{ optional($branch->owner)->name }}</td>
                                        <td>
                                            @if ($branch->manager)
                                                <span class="badge bg-success">{{ $branch->manager->name }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Not Assigned</span>
                                            @endif
                                        </td>
                                        <td>{{ $branch->phone }}</td>
                                        <td>{{ $branch->city }}</td>
                                        <td>
                                            @if ($branch->qrcode)
                                                <img src="{{ asset($branch->qrcode) }}" width="60" height="60" alt="QR Code">
                                            @else
                                                <span class="badge bg-secondary">No QR</span>
                                            @endif
                                        </td>
                                        <td class="text-white">
                                            @php $endDate = optional($branch->activeSubscription)->end_date; @endphp
                                            @if ($endDate)
                                                @if (\Carbon\Carbon::parse($endDate)->diffInDays(now(), false) >= -10 && \Carbon\Carbon::parse($endDate)->isFuture())
                                                    <span class="badge bg-danger">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                                                @elseif(\Carbon\Carbon::parse($endDate)->isPast())
                                                    <span class="badge bg-secondary">Expired</span>
                                                @else
                                                    <span class="badge bg-success">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($branch->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center flex-nowrap gap-1">
                                                @if (auth()->user()->role == 'super_admin')
                                                    <a href="{{ route('branches.show', $branch->id) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" class="delete-form m-0 p-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('restaurant.branches.show', ['restaurant' => request()->route('restaurant'), 'branch' => $branch->id]) }}"
                                                       class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <button type="button" class="btn btn-primary btn-sm assign-manager-btn"
                                                        data-toggle="modal" data-target="#assignManagerModal"
                                                        data-url="{{ route('restaurant.branches.assign-manager', ['restaurant' => request()->route('restaurant'), 'branch' => $branch->id]) }}">
                                                        <i class="fas fa-user-tie"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-success btn-sm upload-qrcode-btn"
                                                        data-toggle="modal" data-target="#uploadQrModal"
                                                        data-id="{{ $branch->id }}">
                                                        <i class="fas fa-qrcode"></i>
                                                    </button>
                                                @endif

                                                <!-- GST Button -->
                                                <button
                                                        type="button"
                                                        class="btn btn-info btn-sm gst-btn"
                                                        data-toggle="modal"
                                                        data-target="#gstModal"

                                                        data-url="{{ route('restaurant.branches.update-gst', [
                                                            'restaurant' => $branch->restaurant,
                                                            'branch' => $branch
                                                        ]) }}"

                                                        data-enabled="{{ $branch->gst_enabled ? 1 : 0 }}"
                                                        data-gst="{{ $branch->gst }}"
                                                        data-cgst="{{ $branch->cgst }}"
                                                        data-sgst="{{ $branch->sgst }}"
                                                        data-gst_number="{{ $branch->gst_number }}"
                                                    >
                                                        <i class="fas fa-percentage"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">No branches found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($branches->hasPages())
                    <div class="card-footer">{{ $branches->links() }}</div>
                @endif
            </div>
        </section>

        @if (auth()->user()->role == 'owner')
            <div class="modal fade" id="uploadQrModal" tabindex="-1" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <form id="uploadQrForm" method="POST"
                            action="{{ route('branches.upload-qrcode', ['restaurant' => request()->route('restaurant')]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="branch_id" id="branch_id">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Upload QR Code
                                </h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>QR Code Image</label>
                                    <input type="file" name="qrcode" class="form-control" accept="image/*" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-success">
                                    Upload
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="assignManagerModal" tabindex="-1" role="dialog" aria-hidden="true">
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
                                    <select name="branch_manager_id" class="form-control" required>
                                        <option value=""> Select Manager</option>
                                        @foreach ($managers as $manager)
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


        <!-- GST Modal -->
        <div class="modal fade" id="gstModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="gstForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="branch_id" id="gst_branch_id">

                        <div class="modal-header">
                            <h5 class="modal-title">Manage GST Details</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Enable GST</label>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="modal_gst_enabled" name="gst_enabled" value="1">
                                        <label class="form-check-label" for="modal_gst_enabled">Apply GST</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>GST Number</label>
                                    <input type="text" name="gst_number" id="modal_gst_number" class="form-control">
                                </div>
                            </div>

                            <div id="modal_gst_fields" style="display: none;">
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label>GST %</label>
                                        <input type="number" step="0.01" name="gst" id="modal_gst" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label>CGST %</label>
                                        <input type="number" step="0.01" id="modal_cgst" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label>SGST %</label>
                                        <input type="number" step="0.01" id="modal_sgst" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save GST Details</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if (auth()->user()->role == 'owner')
            @push('scripts')
                <script>
                    $(document).ready(function() {
                        $('.assign-manager-btn').on('click', function() {
                            $('#assignManagerForm').attr('action', $(this).data('url'));
                        });
                    });
                </script>
            @endpush
        @endif

        @push('scripts')
            <script>
            $(document).ready(function () {

                $(document).on('click', '.upload-qrcode-btn', function () {
                    $('#branch_id').val($(this).data('id'));
                });



                $(document).on('click', '.gst-btn', function () {
                    let btn = $(this);

                    $('#gstForm').attr('action', btn.data('url'));

                    $('#modal_gst_enabled').prop('checked', btn.data('enabled') == 1);
                    $('#modal_gst_number').val(btn.data('gst_number'));
                    $('#modal_gst').val(btn.data('gst'));
                    $('#modal_cgst').val(btn.data('cgst'));
                    $('#modal_sgst').val(btn.data('sgst'));

                    toggleModalGSTSection();
                });

                function toggleModalGSTSection() {
                    if ($('#modal_gst_enabled').is(':checked')) {
                        $('#modal_gst_fields').show();
                    } else {
                        $('#modal_gst_fields').hide();
                    }
                }

                $('#modal_gst_enabled').change(function () {
                    toggleModalGSTSection();
                });

                $('#modal_gst').on('input', function () {

                    let gst = parseFloat($(this).val());

                    if (isNaN(gst)) {
                        $('#modal_cgst').val('');
                        $('#modal_sgst').val('');
                        return;
                    }

                    let half = (gst / 2).toFixed(2);

                    $('#modal_cgst').val(half);
                    $('#modal_sgst').val(half);
                });

            });
            </script>
        @endpush

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
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Deactivate?',
                        text: 'This action can be reverted later.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>

@else
    @php abort(403); @endphp
@endcan
