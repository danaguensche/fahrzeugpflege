<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @vite(['resources/css/main.css', 'resources/js/app.js']) 
    <title>Kalender</title>

</head>

<body>
    <div id="app">
        <main-sidebar></main-sidebar>
        <login-component></login-component>
    </div>
</body>

<script src="{{mix('/js/app.js')}}"></script>

</html>