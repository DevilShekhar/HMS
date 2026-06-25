<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body>

    <h2>
        Hello {{ $customer->name }},
    </h2>

    {!! $offer->description !!}

    <br><br>

    <p>
        Thank you for choosing us.
    </p>
    <p>
        {{ $restaurant->name }}
    </p>

</body>

</html>
