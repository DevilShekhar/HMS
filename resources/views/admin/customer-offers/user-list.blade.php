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
                                        <span class="text-muted">No Offer Available</span>
                                    @endif
                                </td>

                            </tr>
                            <!-- Send Offer Modal -->

                            <!-- Send Offer Modal -->
                            <div class="modal fade" id="offerModal{{ $customer->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form action="{{ route('customer-offers.send') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="mobile_number" value="{{ $customer->mobile_number }}">

                                            <div class="modal-header">
                                                <h5 class="modal-title">Send Offer to {{ $customer->customer_name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Offer Category</label>
                                                    <select name="category" id="category{{ $customer->id }}"
                                                        class="form-control" required>
                                                        <option value="">Select Category</option>
                                                        <option value="birthday">Birthday</option>
                                                        <option value="anniversary">Anniversary</option>
                                                        <option value="other">Other</option>
                                                    </select>
                                                </div>

                                                <!-- Description Field - Hidden by default -->
                                                <div class="mb-3" id="descriptionContainer{{ $customer->id }}"
                                                    style="display: none;">
                                                    <label class="form-label">Offer Description / Message <span
                                                            class="text-danger">*</span></label>
                                                    <textarea name="description" id="description{{ $customer->id }}"
                                                        class="form-control" rows="6"
                                                        placeholder="Write your custom offer message here..."></textarea>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-success">Send Offer</button>
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
            responsive: true,
            autoWidth: false
        });

        // Handle category change with Rich Text Editor support
        $(document).on('change', 'select[name="category"]', function() {
            const selectId = $(this).attr('id');
            const customerId = selectId.replace('category', '');

            const container = $('#descriptionContainer' + customerId);
            const editorId = 'description' + customerId;   // assuming your editor has this id

            if ($(this).val() === 'other') {
                container.show(200);

                const dummyText = `
                    <p>Dear Customer,</p>
                    <p>Thank you for being a valued member of our family.</p>
                    <p>We are pleased to offer you a <strong>special discount</strong> on your next visit.</p>
                    <p>Please show this message at the counter to avail the offer.</p>
                    <p><br></p>
                    <p>Best Regards,<br>
                    <strong>${{ auth()->user()->restaurant->name ?? 'Our Restaurant' }} Team</strong></p>
                `;

                // For Summernote (Most Common)
                if ($.fn.summernote) {
                    $('#' + editorId).summernote('code', dummyText);
                }
                // For TinyMCE
                else if (tinymce) {
                    tinymce.get(editorId).setContent(dummyText);
                }
                // Fallback for plain textarea
                else {
                    $('#' + editorId).val(dummyText.replace(/<[^>]+>/g, ''));
                }

            } else {
                container.hide(200);

                if ($.fn.summernote) {
                    $('#' + editorId).summernote('code', '');
                } else if (tinymce) {
                    tinymce.get(editorId).setContent('');
                } else {
                    $('#' + editorId).val('');
                }
            }
        });
    });
</script>
@endpush

@endsection
