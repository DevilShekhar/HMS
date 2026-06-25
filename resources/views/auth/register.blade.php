<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

    <title>Login</title>

    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bundles/bootstrap-social/bootstrap-social.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="mt-3 card card-primary">
                    <div class="card-header">{{ __('Register') }}</div>

                    <div class="card-body">

                        <form method="POST"
                            action="{{ route('branch.register.submit', [
                                'restaurant' => $restaurant->slug,
                                'branch' => $branch->slug,
                            ]) }}">
                            @csrf

                            <!-- Name -->
                            <div class="row mb-3">
                                <label for="name"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>
                                <div class="col-md-6">
                                    <input id="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name') }}" required autofocus>
                                    @error('name')
                                        <span class="invalid-feedback"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="row mb-3">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
                                <div class="col-md-6">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Mobile Number -->
                            <div class="row mb-3">
                                <label for="phone"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Phone') }}</label>
                                <div class="col-md-6">
                                    <input id="phone" type="text"
                                        class="form-control @error('phone') is-invalid @enderror" name="phone"
                                        value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <span class="invalid-feedback"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="birth_date" class="col-md-4 col-form-label text-md-end">
                                    Date of Birth
                                </label>

                                <div class="col-md-6">
                                    <input id="birth_date" type="date" class="form-control" name="birth_date"
                                        value="{{ old('birth_date') }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="anniversary_date" class="col-md-4 col-form-label text-md-end">
                                    Anniversary Date
                                </label>

                                <div class="col-md-6">
                                    <input id="anniversary_date" type="date" class="form-control"
                                        name="anniversary_date" value="{{ old('anniversary_date') }}">
                                </div>
                            </div>
                            <!-- Password -->
                            <div class="row mb-3">

                                <label for="password" class="col-md-4 col-form-label text-md-end">
                                    {{ __('Password') }}
                                </label>

                                <div class="col-md-6">

                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required>

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                </div>

                            </div>


                            <!-- Confirm Password -->
                            <div class="row mb-3">

                                <label for="password_confirmation" class="col-md-4 col-form-label text-md-end">
                                    {{ __('Confirm Password') }}
                                </label>

                                <div class="col-md-6">

                                    <input id="password_confirmation" type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        name="password_confirmation" required>


                                    @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                </div>

                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                                <div class="text-center mt-3">
                                    Already registered?

                                    <a
                                        href="{{ route('customer.login', [
                                            'restaurant' => $restaurant->slug,
                                            'branch' => $branch->slug,
                                        ]) }}">
                                        Login here
                                    </a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

</body>

</html>
