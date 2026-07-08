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

<style>
        body.login-page{
            margin:0;
            height:100vh;
            background:url('{{ asset("assets/img/login-bg.webp") }}') center center/cover no-repeat fixed;
            font-family:'Poppins',sans-serif;
        }
        .login-overlay{
            position:fixed;
            inset:0;
            background:rgba(0,0,0,.55);
        }
        .login-wrapper{
            position:relative;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            z-index:5;
        }
        .login-card{
            width:430px;
            padding:0px 25px 0px 25px;
            border-radius:28px;
            background:rgba(18,18,18,.90);
            border:2px solid #f96002;
            box-shadow:
            0 0 0 1px rgba(255,170,0,.15),
            0 25px 60px rgba(0,0,0,.65);
            backdrop-filter:blur(12px);
        }
        .login-logo{
            width:110px;
            margin-bottom:18px;
        }
        .logo-area h1{
            color:#fff;
            font-size:40px;
            font-weight:800;
            letter-spacing:1px;
            margin-bottom:0;
            line-height:1;
        }
        .sub-title{
            color:#db5d0a;
            font-size:22px;
            font-weight:700;
            letter-spacing:1px;
        }
        .title-divider{
            margin:22px auto;
            width:220px;
            height:1px;
            background:#3a3a3a;
            position:relative;
        }
        .title-divider span{
            width:10px;
            height:10px;
            background:#f58a1f;
            border-radius:50%;
            position:absolute;
            left:50%;
            top:-4px;
            transform:translateX(-50%);
        }
        .logo-area h3{
            color:#fff;
            font-size:32px;
            font-weight:700;
            margin-bottom:8px;
        }
        .logo-area p{
            color:#a5a5a5;
            font-size:18px;
        }
        .logo-area{
            text-align:center;
            color:#fff;
            margin-bottom:25px;
        }
        .logo-circle{
            width:80px;
            height:80px;
            margin:auto;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            background:linear-gradient(135deg,#ff7a00,#ffb000);
            font-size:35px;
            color:#fff;
        }
        .logo-area h2{
            margin-top:20px;
            font-size:38px;
            font-weight:700;
            letter-spacing:1px;
        }
        .logo-area span{
            color:#ff9800;
            text-transform:uppercase;
            font-weight:600;
            letter-spacing:2px;
        }
        .logo-area p{
            color:#d8d8d8;
            margin-top:20px;
        }
        .input-box{
            position:relative;
            margin-bottom:18px;
        }
        .input-box input:focus{
            border:1px solid #ff8a00;
            background:#272727;
        }
        .input-box i {
            position: absolute;
            left: 20px;
            top: 20px;
            font-size: 18px;
            color: #f96002;
        }
        .eye-icon{
            right:18px;
            left:auto!important;
            color:#999!important;
        }
      
        .input-box input{
            width:100%;
            height:56px;
            padding-left:52px;
            border-radius:12px;
            border:1px solid rgba(255,255,255,.1);
            background:#232323;
            color:#fff;
            transition:.3s;
        }
        .input-box input:focus{
            border-color:#ff9800;
            box-shadow:none;
            background:#282828;
        }
        .login-btn{
            width:95%;
            height:46px;
            border:none;
            border-radius:30px;
            font-size:15px;
            font-weight:700;
            color:#fff;
           background: linear-gradient(116deg, #fe5d02, #fd6406);
            margin-top:15px;
            transition:.3s;
        }
        .login-btn:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 25px rgba(255,140,0,.45);
        }
        .forgot-link{
            text-align:center;
            margin-top:18px;
        }
        .forgot-link a{
            color:#9b9b9b;
            text-decoration:underline;
        }
        .forgot-link a:hover{
            color:#ff8a00;
        }
        .login-btn:hover{
            transform:translateY(-2px);
            box-shadow:0 12px 25px rgba(255,128,0,.35);
        }
        .register-link{
            text-align:center;
            margin-top:25px;
            color:#ddd;
        }
        .register-link a{
            color:#ff9800;
            text-decoration:none;
            font-weight:600;
        }
        .text-danger{
            color:#ff8d8d!important;
        }
        @media(max-width:576px){
            .login-card{
                width:95%;
                padding:30px;
            }
            .logo-area h2{
                font-size:30px;
            }
        }
    </style>
<body class="login-page" style="background: url('{{ asset('assets/img/login-bg.webp') }}') center center / cover no-repeat fixed !important;">
    <div class="login-overlay"></div>
    <div class="login-wrapper">
        <div class="login-card">
            
            <div class="logo-area">
                <img src="{{ asset('assets/img/ehtlogo.webp') }}" class="login-logo" alt="Logo">
                <h1>RESTAURANT</h1>
                <div class="sub-title">MANAGEMENT SYSTEM</div>
                <div class="title-divider">
                    <span></span>
                </div>
                <h3>{{ __('Reset Password') }}</h3>
            </div>

            @if (session('status'))
                <div class="alert alert-success" role="alert" style="margin-bottom: 20px; color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: .25rem; text-align: center;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="input-box">
                    <i class="far fa-envelope"></i>
                    <input id="email" type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    
                    @error('email')
                        <small class="text-danger" style="display: block; margin-top: 5px;">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="login-btn" style="margin-bottom: 10px;">
                    {{ __('SEND PASSWORD RESET LINK') }}
                </button>
            </form>

        </div>
    </div>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

</body>

</html>
