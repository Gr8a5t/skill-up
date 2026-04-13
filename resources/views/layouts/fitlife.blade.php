<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SkillUp — Build practical skills for free')</title>

    <link rel="shortcut icon" href="{{ asset('favicon4.png?v=2') }}" type="image/png">
    <link rel="icon" href="{{ asset('favicon4.png?v=2') }}" type="image/png">

    <link rel="stylesheet" href="{{ asset('fitlife-assets/css/style.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Catamaran:wght@600;700;800;900&family=Rubik:wght@400;500;800&display=swap"
        rel="stylesheet">

    <link rel="preload" as="image" href="{{ asset('fitlife-assets/images/hero-banner.png') }}">
    <link rel="preload" as="image" href="{{ asset('fitlife-assets/images/hero-circle-one.png') }}">
    <link rel="preload" as="image" href="{{ asset('fitlife-assets/images/hero-circle-two.png') }}">
    <link rel="preload" as="image" href="{{ asset('fitlife-assets/images/heart-rate.svg') }}">
    <link rel="preload" as="image" href="{{ asset('fitlife-assets/images/calories.svg') }}">
</head>

<body id="top">
    @include('fitlife.sections.header')

    @yield('content')

    @include('fitlife.sections.footer')

    @include('fitlife.sections.back-to-top')

    <script src="{{ asset('fitlife-assets/js/script.js') }}" defer></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
