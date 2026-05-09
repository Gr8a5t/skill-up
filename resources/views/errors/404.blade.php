<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Page Not Found — SkillUp</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff; /* Matching the image background */
            overflow: hidden;
        }
        .container {
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Keeps aspect ratio but uses the white background */
            display: block;
        }
        a {
            width: 100%;
            height: 100%;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ url('/') }}">
            <img src="{{ asset('fitlife-assets/images/notfound.png') }}" alt="404 Not Found">
        </a>
    </div>
</body>
</html>
