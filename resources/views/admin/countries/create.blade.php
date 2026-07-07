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
                        <h1>Create Country</h1>
                        <p>Add a new country configuration.</p>
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
                <form action="{{ route('countries.store') }}" method="POST">
                    @csrf
                    <div class="premium-card">
                        <div class="premium-card-header">
                            <div class="card-title-group">
                                <div>
                                    <h3>Country Information</h3>
                                    <p>Enter country details below.</p>
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
                                        <input type="text" name="name" value="{{ old('name') }}"
                                            class="form-control premium-input" placeholder="Enter Country Name">
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
                                        <input type="text" name="iso_code" value="{{ old('iso_code') }}"
                                            class="form-control premium-input" placeholder="Example : IN">
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
                                            Currency Name <span>*</span>
                                        </label>
                                        <input type="text" name="currency_code" value="{{ old('currency_code') }}"
                                            class="form-control premium-input" placeholder="Example : INR">

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
                                        <input type="text" name="currency_symbol" value="{{ old('currency_symbol') }}"
                                            class="form-control premium-input" placeholder="Example : ₹">
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

                                        <input type="text" name="timezone" id="timezoneInput" list="timezoneList"
                                            value="{{ old('timezone') }}" class="form-control premium-input"
                                            placeholder="Search for a timezone..." autocomplete="off">

                                        <datalist id="timezoneList">
                                            @foreach(timezone_identifiers_list() as $timezone)
                                                <option value="{{ $timezone }}">
                                            @endforeach
                                        </datalist>

                                        @error('timezone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label><br>

                                        <!-- Only Active option shown during creation -->
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" value="1" checked
                                                required>
                                            <label class="form-check-label">Active</label>
                                        </div>

                                        <!-- Hidden field to ensure status is always 1 on create -->
                                        <input type="hidden" name="status" value="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="premium-card-footer">
                            <button type="submit" class="premium-btn btn-primary">
                                <i class="fas fa-save"></i>
                                Create Country
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
