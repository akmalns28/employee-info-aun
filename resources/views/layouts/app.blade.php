<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/static/icon/favicon.png') }}">

    @include('layouts.partials.styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="page">
        @include('layouts.partials.sidebar')
        <div class="page-wrapper">
            @include('layouts.partials.header')
            <div class="page-body">
                <div class="container-xl">
                    @yield('content')
                </div>
            </div>
            @include('layouts.partials.footer')
        </div>
    </div>

    @stack('modal')
    @stack('scripts')
    @include('layouts.partials.scripts')

    <script>
        console.log("jQuery:", typeof $);
        console.log("DataTables:", typeof $.fn.dataTable);
    </script>

</body>

</html>
