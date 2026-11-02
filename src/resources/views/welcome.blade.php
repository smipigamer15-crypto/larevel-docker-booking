<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', __('Laravel')) }}</title>
    </head>
    <body>
        <div id="app">
            <h1>{{ __('Welcome to') }} {{ config('app.name', __('Laravel')) }}</h1>
        </div>
    </body>
</html>
