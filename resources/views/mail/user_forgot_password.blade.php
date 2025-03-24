<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to Expense Tracker | Password Reset</title>
    <style>
        /* Reset some Bootstrap styles */
        body, figure, h1, h2, h3, p {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #1d2936;
        }

        .container {
            max-width: 600px;
            margin: 50px auto; /* Center horizontally */
            padding: 30px;
            background-color: #354150;
            border-radius: 2px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h4 {
            color: #ffffff;
            font-weight: normal;
            margin-bottom: 10px;
        }

        p {
            color: #ffffff;
            font-size: small;
            margin-bottom: 5px;
        }

        a {
            color: #5ca15e;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            color: #3d7e4f;
        }

        .slogan {
            font-size: small;
            font-weight: bolder;
            font-style: normal;
            text-align: center;
        }

        .verify-button {
            display: inline-block;
            padding: 5px 10px;
            background-color: #48bb78;
            color: #ffffff;
            font-size: small;
            text-decoration: none;
            border-radius: 2px;
            font-weight: bold;
        }

        .verify-button:hover {
            background-color: #5bda90;
            color: #ffffff;
        }

        .copyright {
            font-size: small;
            margin-top: 5px;
            text-align: center;
            color: #888888;
        }
    </style>
</head>

<body>
<div class="container">
    <p class="h4">Hello <strong>{{ $username }}</strong>,</p>
{{--    <p class="h4">Hello <strong>Username</strong>,</p>--}}
    <p>We have received a request to reset your password. Please do so by clicking the button below!.</p>

    <p style="text-align: center;">
        <a href="{{ $resetPassUrl }}" class="verify-button">Reset Password</a>
{{--        <a href="" class="verify-button">Reset Password</a>--}}
    </p>

    @include('mail.slogan')

</div>
</body>

</html>
