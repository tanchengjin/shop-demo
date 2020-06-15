<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', '用户中心') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>
<div>
    @include('layouts.nav')

    <main class="py-4 {{str_replace('.','-',\Illuminate\Support\Facades\Route::currentRouteName()).'-page'}}">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2">
                    @include('layouts.user.left')
                </div>
                <div class="col-md-10">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>
</div>
@yield('javascript')
</body>
</html>
