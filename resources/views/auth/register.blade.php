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
    <style>
    body.login-page {
        margin: 0;
        height: 100vh;
        background: url('{{ asset("assets/img/login-bg.webp") }}') center center/cover no-repeat fixed;
        font-family: 'Poppins', sans-serif;
    }
    .login-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .55);
    }
    .login-wrapper {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px 0;
        z-index: 5;
    }
    /* Expanded container width to perfectly fit 3 fields horizontally */
    .register-card-wide {
        width: 1100px;
        max-width: 95%;
        padding: 40px 35px;
        border-radius: 28px;
        background: rgba(18, 18, 18, .90);
        border: 2px solid #f96002;
        box-shadow: 0 0 0 1px rgba(255, 170, 0, .15), 0 25px 60px rgba(0, 0, 0, .65);
        backdrop-filter: blur(12px);
    }
    .login-logo {
        width: 110px;
        margin-bottom: 18px;
    }
    .logo-area h1 {
        color: #fff;
        font-size: 40px;
        font-weight: 800;
        letter-spacing: 1px;
        margin-bottom: 0;
        line-height: 1;
    }
    .sub-title {
        color: #db5d0a;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: 1px;
    }
    .title-divider {
        margin: 22px auto;
        width: 220px;
        height: 1px;
        background: #3a3a3a;
        position: relative;
    }
    .title-divider span {
        width: 10px;
        height: 10px;
        background: #f58a1f;
        border-radius: 50%;
        position: absolute;
        left: 50%;
        top: -4px;
        transform: translateX(-50%);
    }
    .logo-area h3 {
        color: #fff;
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .logo-area {
        text-align: center;
        color: #fff;
        margin-bottom: 25px;
    }
    
    /* 3-Column Grid Structure */
    .form-grid-row {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
    }

    .input-box {
        position: relative;
        margin-bottom: 5px;
    }
    .input-box i {
        position: absolute;
        left: 20px;
        top: 20px;
        font-size: 18px;
        color: #f96002;
    }
    .input-box input {
        width: 100%;
        height: 56px;
        padding-left: 52px;
        padding-right: 15px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, .1);
        background: #232323;
        color: #fff;
        transition: .3s;
        box-sizing: border-box;
    }
    .input-box input:focus {
        border: 1px solid #ff8a00;
        background: #272727;
        outline: none;
    }
    
    /* Form lower actions centering element */
    .form-actions-area {
        max-width: 400px;
        margin: 15px auto 0 auto;
    }

    .login-btn {
        width: 100%;
        height: 60px;
        border: none;
        border-radius: 10px;
        font-size: 22px;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(116deg, #fe5d02, #fd6406);
        margin-top: 15px;
        transition: .3s;
        cursor: pointer;
    }
    .login-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(255, 128, 0, .35);
    }
    .text-danger {
        color: #ff8d8d !important;
    }

    /* Responsive adjustment for small monitors or mobile layouts */
    @media(max-width: 992px) {
        .form-grid-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media(max-width: 576px) {
        .register-card-wide {
            width: 95%;
            padding: 30px 20px;
        }
        .form-grid-row {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        .logo-area h2 {
            font-size: 30px;
        }
    }
</style>
</head>

<body class="login-page" style="background: url('{{ asset('assets/img/login-bg.webp') }}') center center / cover no-repeat fixed !important;">
    <div class="login-overlay"></div>

    <div class="login-wrapper">
        <div class="register-card-wide">

            <div class="logo-area">
                <img src="{{ asset('assets/img/ehtlogo.webp') }}" class="login-logo" alt="Logo">
                <h1>RESTAURANT</h1>
                <div class="sub-title">MANAGEMENT SYSTEM</div>
                <div class="title-divider">
                    <span></span>
                </div>
                <h3>Customer Registration</h3>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mb-3" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 5px; text-align: left;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('branch.register.submit', ['restaurant' => $restaurant->slug, 'branch' => $branch->slug]) }}">
                @csrf

                <div class="form-grid-row">
                    
                    <div class="input-box">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name" class="@error('name') is-invalid @enderror" placeholder="Enter your full name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <small class="text-danger" style="display: block; margin-top: 5px;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-box">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="@error('email') is-invalid @enderror" placeholder="Enter email address" value="{{ old('email') }}" required>
                        @error('email')
                            <small class="text-danger" style="display: block; margin-top: 5px;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-box">
                        <i class="fas fa-phone"></i>
                        <input type="text" name="phone" class="@error('phone') is-invalid @enderror" placeholder="Enter mobile number" value="{{ old('phone') }}" required>
                        @error('phone')
                            <small class="text-danger" style="display: block; margin-top: 5px;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-box">
                        <i class="fas fa-birthday-cake"></i>
                        <input type="date" name="birth_date" class="@error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}" onclick="this.showPicker()" required>
                        @error('birth_date')
                            <small class="text-danger" style="display: block; margin-top: 5px;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-box">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" name="anniversary_date" class="@error('anniversary_date') is-invalid @enderror" value="{{ old('anniversary_date') }}" onclick="this.showPicker()">
                        @error('anniversary_date')
                            <small class="text-danger" style="display: block; margin-top: 5px;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-box">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="@error('password') is-invalid @enderror" placeholder="Enter password" required>
                        @error('password')
                            <small class="text-danger" style="display: block; margin-top: 5px;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-box">
                        <i class="fas fa-check-circle"></i>
                        <input type="password" name="password_confirmation" class="@error('password_confirmation') is-invalid @enderror" placeholder="Confirm your password" required>
                        @error('password_confirmation')
                            <small class="text-danger" style="display: block; margin-top: 5px;">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                <div class="form-actions-area">
                    <button type="submit" class="login-btn">Register</button>

                    <div class="text-center mb-3" style="color: #ffffff; text-align: center; margin-top: 20px;">
                        Already registered? 
                        <a href="{{ route('customer.login', ['restaurant' => $restaurant->slug, 'branch' => $branch->slug]) }}" style="color: #ff9800; text-decoration: none; font-weight: bold;">
                            Login here
                        </a>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

</body>

</html>
