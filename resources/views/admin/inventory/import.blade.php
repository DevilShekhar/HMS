@can('create-inventory')
    @extends('layouts.app')

    @section('content')

        <section class="section premium-dashboard">
            <div class="premium-floating-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-file-upload"></i>
                        </div>

                        <div>
                            <span class="header-badge">
                                Inventory Management
                            </span>

                            <h1>Bulk Inventory Upload</h1>
                            <p>Upload multiple inventory items using an Excel or CSV file.</p>
                        </div>
                    </div>

                    @php
                        $restaurantSlug = request()->route('restaurant');
                        $branchSlug = request()->route('branch');
                    @endphp

                    <div class="header-right">

                        @if($branchSlug)
                                    <a href="{{ route('branch.inventory.index', [
                                'restaurant' => $restaurantSlug,
                                'branch' => $branchSlug
                            ]) }}" class="premium-back-btn">
                                        <i class="fas fa-arrow-left"></i>
                                        Back To Inventory
                                    </a>
                        @else
                                    <a href="{{ route('restaurant.inventory.index', [
                                'restaurant' => $restaurantSlug
                            ]) }}" class="premium-back-btn">
                                        <i class="fas fa-arrow-left"></i>
                                        Back To Inventory
                                    </a>
                        @endif

                    </div>
                </div>
            </div>
        </section>


        <section class="section premium-dashboard pt-0">

            <div class="card premium-block">

                <div class="card-header premium-card-header">
                    <div>
                        <h4>Upload Inventory File</h4>
                        <p class="header-subtext">
                            Select an Excel (.xlsx/.xls) or CSV file to import inventory items.
                        </p>
                    </div>
                </div>

                @if($branchSlug)
                    <form action="{{ route('branch.inventory.import', [
                        'restaurant' => $restaurantSlug,
                        'branch' => $branchSlug,
                    ]) }}" method="POST" enctype="multipart/form-data">
                @else
                            <form action="{{ route('restaurant.inventory.import', [
                            'restaurant' => $restaurantSlug,
                        ]) }}" method="POST" enctype="multipart/form-data">
                    @endif

                        @csrf

                        <div class="card-body">

                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Please fix the following errors:</strong>

                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif


                            <div class="row">

                                <div class="col-md-12 mb-4">

                                    <label class="premium-label">
                                        Select Excel File <span>*</span>
                                    </label>

                                    <input type="file" name="file" class="form-control premium-input" accept=".xlsx,.xls,.csv"
                                        required>

                                    <small class="text-muted mt-2 d-block">
                                        Supported formats:
                                        <strong>.xlsx</strong>,
                                        <strong>.xls</strong>,
                                        <strong>.csv</strong>
                                    </small>

                                </div>

                                <div class="col-md-12">

                                    <div class="border shadow-sm rounded-3 p-4"
                                        style="background:#fff3e8; border-color:#ffd8b5 !important;">

                                        <h5 class="mb-3" style="color:#d46b08;">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Bulk Upload Instructions
                                        </h5>

                                        <ul class="mb-3 ps-3" style="color:#6b4e16;">
                                            <li>Download and use the sample Excel template.</li>
                                            <li><strong>Do not rename, delete, or reorder the column headers.</strong></li>
                                            <li>Keep the first row as the header row.</li>
                                            <li>Each inventory item must be entered on a separate row.</li>
                                            <li><strong>Item Name</strong> must be unique within the selected branch.</li>
                                            <li><strong>Unit</strong> must be one of:
                                                <code>kg</code>,
                                                <code>gram</code>,
                                                <code>liter</code>,
                                                <code>ml</code>,
                                                <code>packet</code>,
                                                <code>piece</code>.
                                            </li>
                                            <li><strong>Total Stock</strong> and <strong>Minimum Stock</strong> must contain
                                                numeric
                                                values only.</li>
                                            <li>Do not leave any required field blank.</li>
                                            <li>Upload only <strong>.xlsx</strong>, <strong>.xls</strong>, or
                                                <strong>.csv</strong>
                                                files.</li>
                                        </ul>

                                        <hr style="border-color:#ffd8b5;">

                                        <h6 class="mb-2" style="color:#d46b08;">
                                            <i class="fas fa-table me-2"></i>
                                            Required Excel Format
                                        </h6>

                                        <table class="table table-bordered table-sm mb-0 bg-warking">
                                            <thead style="background:#ffe8d1;">
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Unit</th>
                                                    <th>Total Stock</th>
                                                    <th>Minimum Stock</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td>Rice</td>
                                                    <td>kg</td>
                                                    <td>150</td>
                                                    <td>20</td>
                                                </tr>

                                                <tr>
                                                    <td>Cooking Oil</td>
                                                    <td>liter</td>
                                                    <td>50</td>
                                                    <td>10</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="premium-card-footer">

                            <a href="{{ route('restaurant.inventory.sample', [
                                    'restaurant' => request()->route('restaurant')
                                ]) }}" class="premium-btn btn-secondary">

                                <i class="fas fa-download"></i>

                                Download Sample
                            </a>

                            <button type="submit" class="premium-btn btn-primary">

                                <i class="fas fa-file-upload"></i>

                                Upload Inventory

                            </button>

                        </div>

                    </form>

            </div>

        </section>

    @endsection

@else

    @php
        abort(403);
    @endphp

@endcan
