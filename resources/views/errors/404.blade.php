<!DOCTYPE html>
<html>

<head>
    <title>404 Not Found</title>
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
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-top: 4px solid #dc3545;
            /* RED */
            max-width: 400px;
        }

        .code {
            font-size: 48px;
            font-weight: bold;
            color: #dc3545;
            /* RED */
        }

        .msg {
            margin-top: 10px;
            color: #dc3545;
            /* RED */
            font-size: 15px;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 14px;
            background: #dc3545;
            /* RED */
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
        <div class="code">404</div>
        <div class="msg">Oops! The page you are looking for was not found.</div>

        @php
            $homeUrl = route('login');

            if (auth()->check()) {
                if (auth()->user()->role == 'super_admin') {
                    $homeUrl = route('dashboard');
                } elseif (auth()->user()->branch) {
                    $homeUrl = route('branch.dashboard', [
                        'restaurant' => auth()->user()->restaurant->slug,
                        'branch' => auth()->user()->branch->slug,
                    ]);
                } elseif (auth()->user()->restaurant) {
                    $homeUrl = route('restaurant.dashboard', [
                        'restaurant' => auth()->user()->restaurant->slug,
                    ]);
                }
            }
        @endphp

        <a href="{{ $homeUrl }}">Go Home</a>
    </div>

</body>

</html>
