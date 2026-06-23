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

    <div class="loader"></div>

    <div id="app">
        <section class="section">
            <div class="container mt-5">

                <div class="row">
                    <div
                        class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">

                        <div class="card card-primary">

                            <div class="card-header">
                                <h4>Login</h4>
                            </div>

                            <div class="card-body">
                                <form method="POST"
                                    @if (request()->routeIs('customer.login')) action="{{ route('customer.login.submit', [
                                        'restaurant' => request()->route('restaurant'),
                                        'branch' => request()->route('branch'),
                                    ]) }}"

                                            @elseif(request()->route('branch'))

                                            action="{{ route('branch.login.submit', [
                                                'restaurant' => request()->route('restaurant'),
                                                'branch' => request()->route('branch'),
                                            ]) }}"

                                            @elseif(request()->route('restaurant'))

                                            action="{{ route('restaurant.login.submit', [
                                                'restaurant' => request()->route('restaurant'),
                                            ]) }}"

                                            @else

                                            action="{{ route('login') }}" @endif>
                                    @csrf
                                    <div class="form-group">
                                        <label>Email</label>

                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email') }}" required>

                                        @error('email')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Password</label>

                                        <input type="password" name="password" class="form-control" required>

                                        @error('password')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                                            Login
                                        </button>
                                    </div>
                                    @if (request()->routeIs('customer.login'))
                                        <div class="text-center mt-3">
                                            New customer?

                                            <a
                                                href="{{ route('branch.register', [
                                                    'restaurant' => request()->route('restaurant'),
                                                    'branch' => request()->route('branch'),
                                                ]) }}">
                                                Register here
                                            </a>
                                        </div>
                                    @endif

                                </form>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

</body>

</html>
