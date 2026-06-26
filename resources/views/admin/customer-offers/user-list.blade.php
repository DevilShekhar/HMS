@extends('layouts.app')

@section('content')
    <div class="card">

        <div class="card-header">
            <h4>Registered Customers</h4>
        </div>
        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered" id="permissionsTable">

                    <thead>
                        <tr>
                            <th>SrNo</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Date Of Birth</th>
                            <th>Anniversary Date</th>
                            <th>Total Visits</th>
                            <th>Last Visit</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($customers as $customer)
                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    {{ $customer->customer_name }}
                                </td>

                                <td>
                                    {{ $customer->mobile_number }}
                                </td>

                                <td>
                                    {{ $customer->email }}
                                </td>

                                <td>
                                    {{ $customer->birth_date ? \Carbon\Carbon::parse($customer->birth_date)->format('d M Y') : '-' }}
                                </td>

                                <td>
                                    {{ $customer->anniversary_date ?? '-' }}
                                </td>

                                <td>
                                    {{ $customer->total_visits }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($customer->last_visit)->format('d M Y') }}
                                </td>
                                <td>
                                    @if (($customer->birth_date || $customer->anniversary_date) && $customer->email)
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#offerModal{{ $customer->id }}">

                                            Send Offer

                                        </button>
                                    @else
                                        <span class="text-muted">
                                            No Offer Available
                                        </span>
                                    @endif
                                </td>

                            </tr>
                            <!-- Send Offer Modal -->

                            <div class="modal fade" id="offerModal{{ $customer->id }}" tabindex="-1">

                                <div class="modal-dialog">

                                    <div class="modal-content">


                                        <form action="{{ route('customer-offers.send') }}" method="POST">

                                            @csrf


                                            <input type="hidden" name="mobile_number"
                                                value="{{ $customer->mobile_number }}">



                                            <div class="modal-header">

                                                <h5 class="modal-title">

                                                    Send Offer to {{ $customer->name }}

                                                </h5>


                                                <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                </button>

                                            </div>



                                            <div class="modal-body">


                                                <div class="mb-3">

                                                    <label class="form-label">
                                                        Offer Category
                                                    </label>


                                                    <select name="category" class="form-control" required>


                                                        <option value="">
                                                            Select Category
                                                        </option>


                                                        <option value="birthday">
                                                            Birthday
                                                        </option>


                                                        <option value="anniversary">
                                                            Anniversary
                                                        </option>


                                                        <option value="other">
                                                            Other
                                                        </option>


                                                    </select>


                                                </div>


                                            </div>



                                            <div class="modal-footer">


                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                                                    Close

                                                </button>


                                                <button type="submit" class="btn btn-success">

                                                    Send Offer

                                                </button>


                                            </div>


                                        </form>


                                    </div>

                                </div>

                            </div>
                        @endforeach


                    </tbody>

                </table>

            </div>

        </div>

    </div>
    @push('scripts')
        <script>
            $(function() {
                $('#permissionsTable').DataTable({
                    responsive: false,
                    autoWidth: false
                });
            });
        </script>
    @endpush

@endsection
