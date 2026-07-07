@extends('layouts.app')

@section('content')

<section class="section premium-dashboard">
    <div class="premium-floating-header">
        <div class="header-content">

            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-globe-asia"></i>
                </div>

                <div>
                    <span class="header-badge">
                        Country Management
                    </span>

                    <h1>Edit Country</h1>
                    <p>Update country information.</p>
                </div>
            </div>

            <div class="premium-head-actions">

                <a href="{{ route('countries.index') }}" class="premium-back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back To Countries
                </a>

            </div>

        </div>
    </div>
</section>


<section class="section premium-dashboard pt-0">

    <div class="row">

        <div class="col-md-12">

            <form action="{{ route('countries.update',$country->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="premium-card">

                    <div class="premium-card-header">

                        <div class="card-title-group">

                            <div>

                                <h3>Country Information</h3>

                                <p>Modify country details below.</p>

                            </div>

                        </div>

                    </div>


                    <div class="premium-card-body">

                        <div class="row">

                            <div class="col-md-6">

                                <div class="premium-form-group">

                                    <label class="premium-label">

                                        Country Name <span>*</span>

                                    </label>

                                    <input type="text"
                                           name="name"
                                           class="form-control premium-input"
                                           value="{{ old('name',$country->name) }}"
                                           placeholder="Enter Country Name">

                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="premium-form-group">

                                    <label class="premium-label">

                                        ISO Code <span>*</span>

                                    </label>

                                    <input type="text"
                                           name="iso_code"
                                           class="form-control premium-input"
                                           value="{{ old('iso_code',$country->iso_code) }}"
                                           placeholder="Example : IN">

                                    @error('iso_code')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>

                            </div>

                        </div>

                    </div>



                    <div class="premium-card-body">

                        <div class="row">

                            <div class="col-md-6">

                                <div class="premium-form-group">

                                    <label class="premium-label">

                                        Currency Code <span>*</span>

                                    </label>

                                    <input type="text"
                                           name="currency_code"
                                           class="form-control premium-input"
                                           value="{{ old('currency_code',$country->currency_code) }}"
                                           placeholder="Example : INR">

                                    @error('currency_code')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="premium-form-group">

                                    <label class="premium-label">

                                        Currency Symbol <span>*</span>

                                    </label>

                                    <input type="text"
                                           name="currency_symbol"
                                           class="form-control premium-input"
                                           value="{{ old('currency_symbol',$country->currency_symbol) }}"
                                           placeholder="Example : ₹">

                                    @error('currency_symbol')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>

                            </div>

                        </div>

                    </div>




                    <div class="premium-card-body">

                        <div class="row">

                            <div class="col-md-6">

                                <div class="premium-form-group">

                                    <label class="premium-label">

                                        Timezone <span>*</span>

                                    </label>

                                    <select name="timezone"
                                            class="form-control premium-input">

                                        <option value="">
                                            Select Timezone
                                        </option>

                                        @foreach(timezone_identifiers_list() as $timezone)

                                            <option value="{{ $timezone }}"
                                                {{ old('timezone',$country->timezone)==$timezone ? 'selected' : '' }}>

                                                {{ $timezone }}

                                            </option>

                                        @endforeach

                                    </select>

                                    @error('timezone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>

                            </div>



                            <div class="col-md-6">

                                <div class="premium-form-group">

                                    <label class="premium-label">

                                        Status <span>*</span>

                                    </label>

                                    <select name="status"
                                            class="form-control premium-input">

                                        <option value="1"
                                            {{ old('status',$country->status)==1 ? 'selected' : '' }}>

                                            Active

                                        </option>

                                        <option value="0"
                                            {{ old('status',$country->status)==0 ? 'selected' : '' }}>

                                            Inactive

                                        </option>

                                    </select>

                                    @error('status')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>

                            </div>

                        </div>

                    </div>



                    <div class="premium-card-footer">

                        <button type="submit"
                                class="premium-btn btn-primary">

                            <i class="fas fa-save"></i>

                            Update Country

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</section>

@endsection
