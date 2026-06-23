<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

    <title>Customer Login</title>

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
                                <h4>Customer Login</h4>
                            </div>


                            <div class="card-body">


                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif


                                @if ($errors->any())

                                    <div class="alert alert-danger">

                                        @foreach ($errors->all() as $error)
                                            <div>
                                                {{ $error }}
                                            </div>
                                        @endforeach

                                    </div>

                                @endif



                                <form method="POST"
                                    action="{{ route('customer.login.submit', [
                                        'restaurant' => $restaurant->slug,
                                        'branch' => $branch->slug,
                                    ]) }}">

                                    @csrf


                                    <div class="form-group">

                                        <label>
                                            Mobile Number
                                        </label>


                                        <input type="text" name="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone') }}" placeholder="Enter mobile number" required>


                                        @error('phone')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror


                                    </div>
                                    <div class="form-group">

                                        <label>
                                            Password
                                        </label>


                                        <input type="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Enter password" required>


                                        @error('password')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror


                                    </div>



                                    <div class="form-group">

                                        <button type="submit" class="btn btn-primary btn-lg btn-block">

                                            Continue

                                        </button>

                                    </div>



                                </form>


                                <div class="text-center mt-3">

                                    New customer?

                                    <a
                                        href="{{ route('branch.register', [
                                            'restaurant' => $restaurant->slug,
                                            'branch' => $branch->slug,
                                        ]) }}">

                                        Register here

                                    </a>

                                </div>


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
