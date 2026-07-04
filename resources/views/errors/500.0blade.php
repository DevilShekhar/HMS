<!DOCTYPE html>
<html>
<head>
    <title>500 Server Error</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f3f4f6;
            font-family: Arial, sans-serif;
        }

        .card {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
            border-top: 4px solid #dc3545; /* RED */
            max-width: 420px;
        }

        .code {
            font-size: 48px;
            font-weight: bold;
            color: #dc3545;
        }

        .msg {
            margin-top: 10px;
            color: #dc3545;
            font-size: 15px;
        }

        .sub {
            margin-top: 8px;
            font-size: 13px;
            color: #666;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 14px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        a:hover {
            background: #b02a37;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="code">500</div>
    <div class="msg">Something went wrong on our server.</div>
    <div class="sub">We are working to fix it. Please try again later.</div>

    <a href="{{ url('/') }}">Go Home</a>
</div>

</body>
</html>
