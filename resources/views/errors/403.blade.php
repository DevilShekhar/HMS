<!DOCTYPE html>
<html>

<head>
    <title>403 Forbidden</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .error-card {
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-left: 5px solid #dc3545;
        }

        .error-code {
            font-size: 40px;
            font-weight: bold;
            color: #dc3545;
        }

        .error-message {
            margin-top: 10px;
            font-size: 16px;
            color: #dc3545;
        }

        .btn {
            margin-top: 20px;
            display: inline-block;
            padding: 8px 16px;
            background: #dc3545;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="error-card">
        <div class="error-code">403</div>
        <div class="error-message">
            You don’t have permission to access this page.
        </div>

        <a href="{{ url('/') }}" class="btn">Go Home</a>
    </div>

</body>

</html>
